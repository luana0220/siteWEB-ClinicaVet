<?php
//banco de dados do sistema

//array associativo de catgorias da clínica
/*
Consultas: De rotina e retorno
Exames: de raio-x e sangue
Vacinação: anti-raiva e múltipla
Procedimentos: castração e cirurgia
Higiene: escovação dentária e higienização completa(banho e tosa, escovação, orelhas, hidratação)
*/

$categorias = ["Todos","Consultas", "Vacinação", "Procedimentos", "Higiene", "Exames"];

//itens
$itens = [
    [
        'id' => 6,
        'nome' => 'Cirurgia',
        'valor' => 2000.00,
        'categoria' => 'Procedimentos',
        'horarios' => ["09:00", "10:00", "14:00"],
        'imagem' => 'cardCirurgia.png',
        'descricao' => 'Procedimentos cirúrgicos realizados com equipe especializada, garantindo segurança e recuperação adequada do animal.'
    ],
    [
        'id' => 1,
        'nome' => 'Consulta de Rotina',
        'valor' => 120.00,
        'categoria' => 'Consultas',
        'horarios' => ["08:00", "10:00", "14:00", "19:00"],
        'imagem' => 'cardConsultaRotina.png',
        'descricao' => 'Avaliação geral da saúde do animal com exame físico completo e orientações preventivas.'
    ],
    [
        'id' => 3,
        'nome' => 'Raio-x',
        'valor' => 250.00,
        'categoria' => 'Exames',
        'horarios' => ["08:00", "09:00", "14:00", "15:00", "16:00", "17:00", "18:00", "19:00"],
        'imagem' => 'cardRaioX.png',
        'descricao' => 'Exame de imagem utilizado para diagnosticar fraturas, lesões e alterações internas.'
    ],
    [
        'id' => 9,
        'nome' => 'Vacina anti-rábica',
        'valor' => 200.00,
        'categoria' => 'Vacinação',
        'horarios' => ["08:00", "09:00", "14:00", "17:00"],
        'imagem' => 'cardVacRaiva.png',
        'descricao' => 'Vacina essencial para prevenção da raiva, protegendo a saúde do animal e da população.'
    ],
    [
        'id' => 5,
        'nome' => 'Castração',
        'valor' => 1500.00,
        'categoria' => 'Procedimentos',
        'horarios' => ["08:00", "09:00", "10:00"],
        'imagem' => 'cardCastracao.png',
        'descricao' => 'Procedimento cirúrgico que impede a reprodução, contribuindo para a saúde e controle populacional.'
    ],
    [
        'id' => 7,
        'nome' => 'Escovação dentária',
        'valor' => 70.00,
        'categoria' => 'Higiene',
        'horarios' => ["08:00", "09:00", "10:00", "14:00", "15:00", "16:00", "17:00", "18:00", "19:00"],
        'imagem' => 'cardEscovacao.png',
        'descricao' => 'Limpeza dos dentes do animal para prevenir tártaro, mau hálito e doenças bucais.'
    ],
    [
        'id' => 8,
        'nome' => 'Higienização Completa',
        'valor' => 950.00,
        'categoria' => 'Higiene',
        'horarios' => ["08:00", "14:00", "17:00"],
        'imagem' => 'cardHigiene.png',
        'descricao' => 'Serviço completo com banho, tosa, limpeza de orelhas e cuidados com a pelagem.'
    ],
    [
        'id' => 4,
        'nome' => 'Exame de sangue',
        'valor' => 85.50,
        'categoria' => 'Exames',
        'horarios' => ["14:00", "15:00", "16:00"],
        'imagem' => 'cardExameSangue.png',
        'descricao' => 'Coleta e análise de sangue para avaliação da saúde geral e identificação de doenças.'
    ],
    [
        'id' => 10,
        'nome' => 'Vacina múltipla',
        'valor' => 500.00,
        'categoria' => 'Vacinação',
        'horarios' => ["08:00", "09:00", "14:00", "18:00", "19:00"],
        'imagem' => 'cardVacMulti.png',
        'descricao' => 'Vacina que protege contra diversas doenças, fortalecendo o sistema imunológico do animal.'
    ],
    [
        'id' => 2,
        'nome' => 'Consulta de retorno',
        'valor' => 100.00,
        'categoria' => 'Consultas',
        'horarios' => ["16:00", "17:00", "18:00", "19:00"],
        'imagem' => 'cardConsultaRetorno.png',
        'descricao' => 'Consulta para acompanhamento de tratamento ou reavaliação do quadro clínico.']
];

//lista de horários
$horariosDisponiveis = ["08:00", "09:00", "10:00", "14:00", "15:00", "16:00", "17:00", "18:00", "19:00"];

//array para deixar cada filtro de uma cor
$cores = [
    'Todos' => 'bg-dark',
    'Consultas' => 'bg-info',
    'Vacinação' => 'bg-success',
    'Procedimentos' => 'bg-danger',
    'Higiene' => 'bg-primary',
    'Exames' => 'bg-secondary'
];


$coresEspecies = [
    'Cachorro' => 'danger',
    'Gato' => 'primary',
    'Coelho' => 'warning'
];

$coresSexo = [
    'Femea' => 'danger',
    'Macho' => 'success'
];
function buscarItem($id, $itens) {
    foreach ($itens as $item) {
        if($item['id'] == $id) {
            return $item;
        }
    }
    return null;
}
?>