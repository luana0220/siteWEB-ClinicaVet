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
        $sql  = "SELECT id FROM horarios_disponiveis
                  WHERE servico_id = :servico_id AND data_marcada = :data AND horario = :horario AND disponivel = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['servico_id' => $servico_id, 'data' => $data, 'horario' => $horario . ':00']);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function validarAtendimento(int $servico_id, string $nome, string $especie, string $sexo, string $idade, string $data, string $horario, array $escolhidos): array
    {
        $erros = [];

        if ($nome === '' || $especie === '' || $sexo === '' || $idade === '' || $data === '' || $horario === '') {
            $erros[] = "Nome, espécie, sexo, idade, data e horário são obrigatórios.";
        }
        if (mb_strlen($nome) > 100) {
            $erros[] = "O nome do animal deve ter no máximo 100 caracteres.";
        }
        if (mb_strlen($especie) > 30) {
            $erros[] = "A espécie deve ter no máximo 30 caracteres.";
        }
        if ($sexo !== '' && !isset($this->sexos()[$sexo])) {
            $erros[] = "Sexo inválido.";
        }
        if ($idade !== '' && (!ctype_digit($idade) || (int) $idade > 100)) {
            $erros[] = "A idade deve ser um número inteiro entre 0 e 100.";
        }
        if ($data !== '') {
            $partes = explode('-', $data);
            if (count($partes) !== 3 || !checkdate((int) $partes[1], (int) $partes[2], (int) $partes[0])) {
                $erros[] = "Data inválida.";
            } elseif ($data < date('Y-m-d')) {
                $erros[] = "A data não pode estar no passado.";
            }
        }
        if ($horario !== '' && !in_array($horario, $this->horarios())) {
            $erros[] = "Horário inválido.";
        }

        if (empty($erros)) {
            foreach ($escolhidos as $escolhido) {
                if ($escolhido['servico_id'] === $servico_id && $escolhido['data'] === $data && $escolhido['horario'] === $horario) {
                    $erros[] = "Você já escolheu este serviço nesta data e horário.";
                }
            }
        }
        if (empty($erros) && $this->horarioOcupado($servico_id, $data, $horario)) {
            $erros[] = "Este horário já está reservado para o serviço escolhido.";
        }

        return $erros;
    }

    public function validarSolicitacao(string $telefone, array $atendimentos): array
    {
        $erros = [];

        if (empty($atendimentos)) {
            $erros[] = "Adicione pelo menos um atendimento antes de enviar a solicitação.";
        }
        if ($telefone === '') {
            $erros[] = "O telefone é obrigatório.";
        }
        if (mb_strlen($telefone) > 20) {
            $erros[] = "O telefone deve ter no máximo 20 caracteres.";
        }

        foreach ($atendimentos as $atendimento) {
            if ($this->horarioOcupado($atendimento['servico_id'], $atendimento['data'], $atendimento['horario'])) {
                $erros[] = "O horário de " . $atendimento['nome'] . " (" . $atendimento['servico'] . ") já foi reservado. Remova este atendimento e escolha outro horário.";
            }
        }

        return $erros;
    }

    private function reservarHorario(int $servico_id, string $data, string $horario): int
    {
        $dados = ['servico_id' => $servico_id, 'data' => $data, 'horario' => $horario . ':00'];
        $busca = "SELECT id FROM horarios_disponiveis
                   WHERE servico_id = :servico_id AND data_marcada = :data AND horario = :horario";

        $stmt = $this->pdo->prepare($busca);
        $stmt->execute($dados);
        $linha = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($linha) {
            $stmt = $this->pdo->prepare("UPDATE horarios_disponiveis SET disponivel = 0 WHERE id = :id");
            $stmt->execute(['id' => $linha['id']]);
            return (int) $linha['id'];
        }

        $stmt = $this->pdo->prepare("INSERT INTO horarios_disponiveis (servico_id, data_marcada, horario, disponivel)
                                     VALUES (:servico_id, :data, :horario, 0)");
        $stmt->execute($dados);

        $stmt = $this->pdo->prepare($busca);
        $stmt->execute($dados);
        $linha = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $linha['id'];
    }

    public function inserir(int $usuario_id, string $telefone, array $atendimentos): void
    {
        $total = 0;
        foreach ($atendimentos as $atendimento) {
            $total += $atendimento['valor'];
        }

        $sql  = "INSERT INTO solicitacoes (usuario_id, servico_id, telefone, quantidade, valor_total)
                 VALUES (:usuario_id, :servico_id, :telefone, :quantidade, :valor_total)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'usuario_id'  => $usuario_id,
            'servico_id'  => $atendimentos[0]['servico_id'],
            'telefone'    => $telefone,
            'quantidade'  => count($atendimentos),
            'valor_total' => $total
        ]);

        $stmt = $this->pdo->prepare("SELECT id FROM solicitacoes WHERE usuario_id = :usuario_id ORDER BY id DESC");
        $stmt->execute(['usuario_id' => $usuario_id]);
        $solicitacao_id = (int) $stmt->fetch(PDO::FETCH_ASSOC)['id'];

        foreach ($atendimentos as $atendimento) {
            $horario_id = $this->reservarHorario($atendimento['servico_id'], $atendimento['data'], $atendimento['horario']);

            $sql  = "INSERT INTO animais (solicitacao_id, horario_id, nome, especie, sexo, idade)
                     VALUES (:solicitacao_id, :horario_id, :nome, :especie, :sexo, :idade)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'solicitacao_id' => $solicitacao_id,
                'horario_id'     => $horario_id,
                'nome'           => $atendimento['nome'],
                'especie'        => $atendimento['especie'],
                'sexo'           => $atendimento['sexo'],
                'idade'          => $atendimento['idade']
            ]);

            $stmt = $this->pdo->prepare("SELECT id FROM animais WHERE solicitacao_id = :solicitacao_id ORDER BY id DESC");
            $stmt->execute(['solicitacao_id' => $solicitacao_id]);
            $animal_id = (int) $stmt->fetch(PDO::FETCH_ASSOC)['id'];

            $sql  = "INSERT INTO itens_solicitacao (solicitacao_id, animal_id, servico_id, horario_id, valor)
                     VALUES (:solicitacao_id, :animal_id, :servico_id, :horario_id, :valor)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'solicitacao_id' => $solicitacao_id,
                'animal_id'      => $animal_id,
                'servico_id'     => $atendimento['servico_id'],
                'horario_id'     => $horario_id,
                'valor'          => $atendimento['valor']
            ]);
        }
    }

    private function consulta(): string
    {
        return "SELECT itens_solicitacao.id AS item_id, itens_solicitacao.valor, itens_solicitacao.status_item,
                       itens_solicitacao.cancelado_em, solicitacoes.id AS solicitacao_id, solicitacoes.telefone,
                       solicitacoes.status_solicitacao, solicitacoes.valor_total, solicitacoes.data_solicitacao,
                       usuarios.nome AS cliente, animais.nome AS animal, animais.especie, animais.sexo, animais.idade,
                       servicos.nome AS servico, horarios_disponiveis.data_marcada, horarios_disponiveis.horario
                  FROM itens_solicitacao
                  JOIN solicitacoes ON solicitacoes.id = itens_solicitacao.solicitacao_id
                  JOIN usuarios ON usuarios.id = solicitacoes.usuario_id
                  JOIN animais ON animais.id = itens_solicitacao.animal_id
                  JOIN servicos ON servicos.id = itens_solicitacao.servico_id
                  JOIN horarios_disponiveis ON horarios_disponiveis.id = itens_solicitacao.horario_id";
    }

    public function listarDoUsuario(int $usuario_id): array
    {
        $sql  = $this->consulta() . " WHERE solicitacoes.usuario_id = :usuario_id
                                   ORDER BY solicitacoes.id DESC, itens_solicitacao.id ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTodas(): array
    {
        $sql  = $this->consulta() . " ORDER BY solicitacoes.id DESC, itens_solicitacao.id ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function atualizarStatus(int $solicitacao_id): void
    {
        $stmt = $this->pdo->prepare("SELECT status_item FROM itens_solicitacao WHERE solicitacao_id = :solicitacao_id");
        $stmt->execute(['solicitacao_id' => $solicitacao_id]);

        $pendentes   = 0;
        $confirmadas = 0;
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $item) {
            if ($item['status_item'] === 'pendente') {
                $pendentes++;
            }
            if ($item['status_item'] === 'confirmada') {
                $confirmadas++;
            }
        }

        $status = 'cancelada';
        if ($pendentes > 0) {
            $status = 'pendente';
        } elseif ($confirmadas > 0) {
            $status = 'confirmada';
        }

        $stmt = $this->pdo->prepare("UPDATE solicitacoes SET status_solicitacao = :status WHERE id = :id");
        $stmt->execute(['status' => $status, 'id' => $solicitacao_id]);
    }

    private function cancelarItens(array $itens): void
    {
        foreach ($itens as $item) {
            $stmt = $this->pdo->prepare("UPDATE itens_solicitacao SET status_item = 'cancelada', cancelado_em = :agora WHERE id = :id");
            $stmt->execute(['agora' => date('Y-m-d H:i:s'), 'id' => $item['id']]);

            $stmt = $this->pdo->prepare("UPDATE horarios_disponiveis SET disponivel = 1 WHERE id = :id");
            $stmt->execute(['id' => $item['horario_id']]);
        }
    }

    public function cancelarItem(int $usuario_id, int $item_id): bool
    {
        $sql  = "SELECT itens_solicitacao.id, itens_solicitacao.horario_id, itens_solicitacao.solicitacao_id
                   FROM itens_solicitacao
                   JOIN solicitacoes ON solicitacoes.id = itens_solicitacao.solicitacao_id
                  WHERE itens_solicitacao.id = :id AND solicitacoes.usuario_id = :usuario_id
                    AND itens_solicitacao.status_item <> 'cancelada'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $item_id, 'usuario_id' => $usuario_id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item === false) {
            return false;
        }

        $this->cancelarItens([$item]);
        $this->atualizarStatus((int) $item['solicitacao_id']);
        return true;
    }

    public function cancelar(int $usuario_id, int $solicitacao_id): bool
    {
        $sql  = "SELECT itens_solicitacao.id, itens_solicitacao.horario_id
                   FROM itens_solicitacao
                   JOIN solicitacoes ON solicitacoes.id = itens_solicitacao.solicitacao_id
                  WHERE solicitacoes.id = :id AND solicitacoes.usuario_id = :usuario_id
                    AND itens_solicitacao.status_item <> 'cancelada'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $solicitacao_id, 'usuario_id' => $usuario_id]);
        $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($itens)) {
            return false;
        }

        $this->cancelarItens($itens);
        $this->atualizarStatus($solicitacao_id);
        return true;
    }

    public function confirmarItem(int $item_id): bool
    {
        $stmt = $this->pdo->prepare("SELECT solicitacao_id FROM itens_solicitacao WHERE id = :id AND status_item = 'pendente'");
        $stmt->execute(['id' => $item_id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item === false) {
            return false;
        }

        $stmt = $this->pdo->prepare("UPDATE itens_solicitacao SET status_item = 'confirmada' WHERE id = :id");
        $stmt->execute(['id' => $item_id]);

        $this->atualizarStatus((int) $item['solicitacao_id']);
        return true;
    }
}
