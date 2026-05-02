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

$categorias = ["Todos","Consultas", "Vacinacao", "Procedimentos", "Higiene", "Exames"];

//itens
$itens = [
    ['id' => 6, 'nome' => 'Cirurgia', 'valor' => 2000.00, 'categoria' => 'Procedimentos', 'horarios' => ["09:00", "10:00", "14:00"], 'imagem' => 'cardCirurgia.png'],
    ['id' => 1, 'nome' => 'Consulta de Rotina', 'valor' => 120.00, 'categoria' => 'Consultas', 'horarios' => ["08:00", "10:00", "14:00", "19:00"], 'imagem' => 'cardConsultaRotina.png'],
    ['id' => 3, 'nome' => 'Raio-x', 'valor' => 250.00, 'categoria' => 'Exames', 'horarios' => ["08:00", "09:00", "10:00", "14:00", "15:00", "16:00", "17:00", "18:00", "19:00"], 'imagem' => 'cardRaioX.png'],
    ['id' => 9, 'nome' => 'Vacina anti-rábica', 'valor' => 200.00, 'categoria' => 'Vacinacao', 'horarios' => ["08:00", "09:00", "14:00", "17:00"], 'imagem' => 'cardVacRaiva.png'],
    ['id' => 5, 'nome' => 'Castração', 'valor' => 1500.00, 'categoria' => 'Procedimentos', 'horarios' => ["08:00", "09:00", "10:00"], 'imagem' => 'cardCastracao.png'],
    ['id' => 7, 'nome' => 'Escovação dentária', 'valor' => 70.00, 'categoria' => 'Higiene', 'horarios' => ["08:00", "09:00", "10:00", "14:00", "15:00", "16:00", "17:00", "18:00", "19:00"], 'imagem' => 'cardEscovacao.png'],
    ['id' => 8, 'nome' => 'Higienização Completa', 'valor' => 950.00, 'categoria' => 'Higiene', 'horarios' => ["08:00", "14:00", "17:00"], 'imagem' => 'cardHigiene.png'],
    ['id' => 4, 'nome' => 'Exame de sangue', 'valor' => 85.50, 'categoria' => 'Exames', 'horarios' => ["14:00", "15:00", "16:00"], 'imagem' => 'cardExameSangue.png'],
    ['id' => 10, 'nome' => 'Vacina múltipla', 'valor' => 500.00, 'categoria' => 'Vacinacao', 'horarios' => ["08:00", "09:00", "14:00","18:00", "19:00"], 'imagem' => 'cardVacMulti.png'],
    ['id' => 2, 'nome' => 'Consulta de retorno', 'valor' => 100.00, 'categoria' => 'Consultas', 'horarios' => ["16:00", "17:00", "18:00", "19:00"], 'imagem' => 'cardConsultaRetorno.png']
    

];

//lista de horários
$horariosDisponiveis = ["08:00", "09:00", "10:00", "14:00", "15:00", "16:00", "17:00", "18:00", "19:00"];

//array para deixar cada filtro de uma cor
$cores = [
    'Todos' => 'bg-dark',
    'Consultas' => 'bg-info',
    'Vacinacao' => 'bg-success',
    'Procedimentos' => 'bg-danger',
    'Higiene' => 'bg-primary',
    'Exames' => 'bg-secondary'
];

function buscarItem($id, $itens) {
    foreach ($itens as $item) {
        if($item['id'] == $id) {
            return $item;
        }
    }
    return false;
}
?>