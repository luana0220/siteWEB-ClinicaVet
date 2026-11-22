<?php
class SolicitacaoModel extends Model
{
    public function horarios(): array
    {
        return ['08:00', '09:00', '10:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00'];
    }

    public function sexos(): array
    {
        return ['macho' => 'Macho', 'femea' => 'Fêmea'];
    }

    public function horarioOcupado(int $servico_id, string $data, string $horario): bool
    {
        $sql = 'SELECT id FROM horarios_disponiveis WHERE servico_id = :servico AND data_marcada = :data AND horario = :horario AND disponivel = 0';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['servico' => $servico_id, 'data' => $data, 'horario' => $horario . ':00']);
        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function validarAtendimento(int $servico_id, string $nome, string $especie, string $sexo, string $idade, string $data, string $horario, array $escolhidos): array
    {
        $erros = [];
        if ($servico_id <= 0 || trim($nome) === '' || trim($especie) === '' || $sexo === '' || $idade === '' || $data === '' || $horario === '') {
            $erros[] = 'Serviço, nome, espécie, sexo, idade, data e horário são obrigatórios.';
        }
        if (mb_strlen($nome) > 100)
            $erros[] = 'O nome do animal deve ter no máximo 100 caracteres.';
        if (mb_strlen($especie) > 30)
            $erros[] = 'A espécie deve ter no máximo 30 caracteres.';
        if ($sexo !== '' && !array_key_exists($sexo, $this->sexos()))
            $erros[] = 'Sexo inválido.';
        if ($idade !== '' && (!ctype_digit($idade) || (int) $idade > 100))
            $erros[] = 'A idade deve ser um número inteiro entre 0 e 100.';
        if ($data !== '') {
            $d = DateTimeImmutable::createFromFormat('!Y-m-d', $data);
            if (!$d || $d->format('Y-m-d') !== $data)
                $erros[] = 'Data inválida.';
            elseif ($data < date('Y-m-d'))
                $erros[] = 'A data não pode estar no passado.';
        }
        if ($horario !== '' && !in_array($horario, $this->horarios(), true))
            $erros[] = 'Horário inválido.';
        foreach ($escolhidos as $escolhido) {
            if ((int) ($escolhido['servico_id'] ?? 0) === $servico_id && ($escolhido['data'] ?? '') === $data && ($escolhido['horario'] ?? '') === $horario) {
                $erros[] = 'Você já escolheu este serviço nesta data e horário.';
                break;
            }
        }
        if (!$erros && $this->horarioOcupado($servico_id, $data, $horario))
            $erros[] = 'Este horário já está reservado para o serviço escolhido.';
        return $erros;
    }

    public function validarSolicitacao(string $telefone, array $atendimentos): array
    {
        $erros = [];
        if (!$atendimentos)
            $erros[] = 'Adicione pelo menos um atendimento antes de enviar a solicitação.';
        if (trim($telefone) === '')
            $erros[] = 'O telefone é obrigatório.';
        if (mb_strlen($telefone) > 20)
            $erros[] = 'O telefone deve ter no máximo 20 caracteres.';
        $escolhidos = [];
        foreach ($atendimentos as $atendimento) {
            $erros = array_merge($erros, $this->validarAtendimento(
                (int) ($atendimento['servico_id'] ?? 0),
                (string) ($atendimento['nome'] ?? ''),
                (string) ($atendimento['especie'] ?? ''),
                (string) ($atendimento['sexo'] ?? ''),
                (string) ($atendimento['idade'] ?? ''),
                (string) ($atendimento['data'] ?? ''),
                (string) ($atendimento['horario'] ?? ''),
                $escolhidos
            ));
            $escolhidos[] = $atendimento;
        }
        return array_values(array_unique($erros));
    }

    // Chamar somente dentro de uma transação. A atualização condicional evita dupla reserva.
    private function reservarHorario(int $servico_id, string $data, string $horario): int
    {
        $dados = ['servico' => $servico_id, 'data' => $data, 'horario' => $horario . ':00'];
        $stmt = $this->pdo->prepare('INSERT INTO horarios_disponiveis (servico_id, data_marcada, horario, disponivel) VALUES (:servico, :data, :horario, 1) ON DUPLICATE KEY UPDATE id = id');
        $stmt->execute($dados);

        $stmt = $this->pdo->prepare('UPDATE horarios_disponiveis SET disponivel = 0 WHERE servico_id = :servico AND data_marcada = :data AND horario = :horario AND disponivel = 1');
        $stmt->execute($dados);
        if ($stmt->rowCount() !== 1)
            throw new RuntimeException('Este horário já está reservado. Escolha outro.');

        $stmt = $this->pdo->prepare('SELECT id FROM horarios_disponiveis WHERE servico_id = :servico AND data_marcada = :data AND horario = :horario');
        $stmt->execute($dados);
        return (int) $stmt->fetchColumn();
    }

    public function inserir(int $usuario_id, string $telefone, array $atendimentos): void
    {
        $erros = $this->validarSolicitacao($telefone, $atendimentos);
        if ($erros)
            throw new InvalidArgumentException(implode(' ', $erros));

        $this->pdo->beginTransaction();
        try {
            $precos = [];
            $consultaPreco = $this->pdo->prepare('SELECT valor FROM servicos WHERE id = :id');
            $totalCentavos = 0;
            foreach ($atendimentos as $atendimento) {
                $servico = (int) $atendimento['servico_id'];
                if (!isset($precos[$servico])) {
                    $consultaPreco->execute(['id' => $servico]);
                    $valor = $consultaPreco->fetchColumn();
                    if ($valor === false)
                        throw new InvalidArgumentException('Serviço não encontrado.');
                    $precos[$servico] = (string) $valor;
                }
                $totalCentavos += (int) round((float) $precos[$servico] * 100);
            }

            $stmt = $this->pdo->prepare('INSERT INTO solicitacoes (usuario_id, servico_id, telefone, quantidade, valor_total) VALUES (:usuario, :servico, :telefone, :quantidade, :total)');
            $stmt->execute([
                'usuario' => $usuario_id,
                'servico' => (int) $atendimentos[0]['servico_id'],
                'telefone' => $telefone,
                'quantidade' => count($atendimentos),
                'total' => number_format($totalCentavos / 100, 2, '.', '')
            ]);
            $solicitacao_id = (int) $this->pdo->lastInsertId();

            $inserirAnimal = $this->pdo->prepare('INSERT INTO animais (solicitacao_id, horario_id, nome, especie, sexo, idade) VALUES (:solicitacao, :horario, :nome, :especie, :sexo, :idade)');
            $inserirItem = $this->pdo->prepare('INSERT INTO itens_solicitacao (solicitacao_id, animal_id, servico_id, horario_id, valor) VALUES (:solicitacao, :animal, :servico, :horario, :valor)');
            foreach ($atendimentos as $atendimento) {
                $servico = (int) $atendimento['servico_id'];
                $horario_id = $this->reservarHorario($servico, $atendimento['data'], $atendimento['horario']);
                $inserirAnimal->execute([
                    'solicitacao' => $solicitacao_id,
                    'horario' => $horario_id,
                    'nome' => trim($atendimento['nome']),
                    'especie' => trim($atendimento['especie']),
                    'sexo' => $atendimento['sexo'],
                    'idade' => (int) $atendimento['idade']
                ]);
                $animal_id = (int) $this->pdo->lastInsertId();
                $inserirItem->execute([
                    'solicitacao' => $solicitacao_id,
                    'animal' => $animal_id,
                    'servico' => $servico,
                    'horario' => $horario_id,
                    'valor' => $precos[$servico]
                ]);
            }
            $this->pdo->commit();
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction())
                $this->pdo->rollBack();
            throw $e;
        }
    }

    private function consulta(): string
    {
        return 'SELECT itens_solicitacao.id AS item_id, itens_solicitacao.valor, itens_solicitacao.status_item,
                       itens_solicitacao.cancelado_em, solicitacoes.id AS solicitacao_id, solicitacoes.telefone,
                       solicitacoes.status_solicitacao, solicitacoes.valor_total, solicitacoes.data_solicitacao,
                       usuarios.nome AS cliente, animais.nome AS animal, animais.especie, animais.sexo, animais.idade,
                       servicos.nome AS servico, horarios_disponiveis.data_marcada, horarios_disponiveis.horario
                FROM itens_solicitacao
                JOIN solicitacoes ON solicitacoes.id = itens_solicitacao.solicitacao_id
                JOIN usuarios ON usuarios.id = solicitacoes.usuario_id
                JOIN animais ON animais.id = itens_solicitacao.animal_id
                JOIN servicos ON servicos.id = itens_solicitacao.servico_id
                JOIN horarios_disponiveis ON horarios_disponiveis.id = itens_solicitacao.horario_id';
    }

    public function listarDoUsuario(int $usuario_id): array
    {
        $stmt = $this->pdo->prepare($this->consulta() . ' WHERE solicitacoes.usuario_id = :usuario ORDER BY solicitacoes.id DESC, itens_solicitacao.id ASC');
        $stmt->execute(['usuario' => $usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTodas(): array
    {
        $stmt = $this->pdo->prepare($this->consulta() . ' ORDER BY solicitacoes.id DESC, itens_solicitacao.id ASC');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function atualizarStatus(int $solicitacao_id): void
    {
        $stmt = $this->pdo->prepare('SELECT status_item FROM itens_solicitacao WHERE solicitacao_id = :id');
        $stmt->execute(['id' => $solicitacao_id]);
        $estados = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $status = 'cancelada';
        if (in_array('pendente', $estados, true))
            $status = 'pendente';
        elseif (in_array('confirmada', $estados, true))
            $status = 'confirmada';
        // O ENUM atual não permite "parcialmente cancelada"; os itens preservam os estados individuais.
        $stmt = $this->pdo->prepare('UPDATE solicitacoes SET status_solicitacao = :status WHERE id = :id');
        $stmt->execute(['status' => $status, 'id' => $solicitacao_id]);
    }

    // O bloqueio da solicitação-pai serializa alterações em seus itens.
    private function bloquearSolicitacao(int $solicitacao_id, ?int $usuario_id = null): bool
    {
        $sql = 'SELECT id FROM solicitacoes WHERE id = :id';
        $params = ['id' => $solicitacao_id];
        if ($usuario_id !== null) {
            $sql .= ' AND usuario_id = :usuario';
            $params['usuario'] = $usuario_id;
        }
        $stmt = $this->pdo->prepare($sql . ' FOR UPDATE');
        $stmt->execute($params);
        return $stmt->fetchColumn() !== false;
    }

    private function cancelarItens(array $itens): void
    {
        $atualizar = $this->pdo->prepare("UPDATE itens_solicitacao SET status_item = 'cancelada', cancelado_em = NOW() WHERE id = :id AND status_item <> 'cancelada'");
        $liberar = $this->pdo->prepare('UPDATE horarios_disponiveis SET disponivel = 1 WHERE id = :id');
        foreach ($itens as $item) {
            $atualizar->execute(['id' => $item['id']]);
            if ($atualizar->rowCount() !== 1)
                continue;
            // Um horário deve ter somente um item ativo; se houver dados legados duplicados, não o libera.
            $verificar = $this->pdo->prepare("SELECT COUNT(*) FROM itens_solicitacao WHERE horario_id = :id AND status_item <> 'cancelada'");
            $verificar->execute(['id' => $item['horario_id']]);
            if ((int) $verificar->fetchColumn() === 0)
                $liberar->execute(['id' => $item['horario_id']]);
        }
    }

    public function cancelarItem(int $usuario_id, int $item_id): bool
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare('SELECT solicitacao_id FROM itens_solicitacao WHERE id = :id');
            $stmt->execute(['id' => $item_id]);
            $solicitacao_id = $stmt->fetchColumn();
            if ($solicitacao_id === false || !$this->bloquearSolicitacao((int) $solicitacao_id, $usuario_id)) {
                $this->pdo->rollBack();
                return false;
            }
            $stmt = $this->pdo->prepare("SELECT id, horario_id FROM itens_solicitacao WHERE id = :id AND solicitacao_id = :solicitacao AND status_item <> 'cancelada' FOR UPDATE");
            $stmt->execute(['id' => $item_id, 'solicitacao' => $solicitacao_id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$item) {
                $this->pdo->rollBack();
                return false;
            }
            $this->cancelarItens([$item]);
            $this->atualizarStatus((int) $solicitacao_id);
            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction())
                $this->pdo->rollBack();
            throw $e;
        }
    }

    public function cancelar(int $usuario_id, int $solicitacao_id): bool
    {
        $this->pdo->beginTransaction();
        try {
            if (!$this->bloquearSolicitacao($solicitacao_id, $usuario_id)) {
                $this->pdo->rollBack();
                return false;
            }
            $stmt = $this->pdo->prepare("SELECT id, horario_id FROM itens_solicitacao WHERE solicitacao_id = :id AND status_item <> 'cancelada' ORDER BY id FOR UPDATE");
            $stmt->execute(['id' => $solicitacao_id]);
            $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$itens) {
                $this->pdo->rollBack();
                return false;
            }
            $this->cancelarItens($itens);
            $this->atualizarStatus($solicitacao_id);
            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction())
                $this->pdo->rollBack();
            throw $e;
        }
    }

    public function confirmarItem(int $item_id): bool
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare('SELECT solicitacao_id FROM itens_solicitacao WHERE id = :id');
            $stmt->execute(['id' => $item_id]);
            $solicitacao_id = $stmt->fetchColumn();
            if ($solicitacao_id === false || !$this->bloquearSolicitacao((int) $solicitacao_id)) {
                $this->pdo->rollBack();
                return false;
            }
            $stmt = $this->pdo->prepare("UPDATE itens_solicitacao SET status_item = 'confirmada' WHERE id = :id AND solicitacao_id = :solicitacao AND status_item = 'pendente'");
            $stmt->execute(['id' => $item_id, 'solicitacao' => $solicitacao_id]);
            if ($stmt->rowCount() !== 1) {
                $this->pdo->rollBack();
                return false;
            }
            $this->atualizarStatus((int) $solicitacao_id);
            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction())
                $this->pdo->rollBack();
            throw $e;
        }
    }
}
