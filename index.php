<?php

declare(strict_types=1);

require_once __DIR__ . '/Controller/AguaController.php';
require_once __DIR__ . '/Controller/BiofiltroController.php';

$agua = new AguaController();
$biofiltro = new BiofiltroController();

$analise = null;
$bio = null;
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'analisar') {
        $analise = $agua->analisarAmostra($_POST);
        if (!$analise['valida']) {
            $erros = $analise['erros'];
        }
    } elseif ($acao === 'biofiltro') {
        $bio = $biofiltro->comparar($_POST['antes'] ?? [], $_POST['depois'] ?? []);
        if (!$bio['valido']) {
            $erros = $bio['erros'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratório Digital — Qualidade da Água</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="templates/css/style.css">
</head>
<body>
<header class="topo text-center">
    <h1>Laboratório Digital da Água</h1>
    <p>Análise de potabilidade e eficiência do biofiltro — ODS 6: Água potável e saneamento</p>
</header>

<main class="container my-4">
    <?php require __DIR__ . '/View/resultado.php'; ?>
    <?php require __DIR__ . '/View/formulario.php'; ?>

    <div class="card mb-4">
        <div class="card-header">Padrões de referência (Portaria GM/MS nº 888/2021)</div>
        <div class="card-body">
            <ul class="mb-0">
                <li>pH: 6,0 a 9,5</li>
                <li>Turbidez: máximo 5,0 uT</li>
                <li>Cloro residual livre: 0,2 a 5,0 mg/L</li>
                <li>Dureza total: máximo 500 mg/L em CaCO3</li>
                <li>Cor aparente: máximo 15 uH</li>
                <li>Temperatura: informativa (sem limite legal; referência operacional de 25 °C)</li>
            </ul>
        </div>
    </div>
</main>

<footer class="text-center pb-4">
    <small>Trabalho interárea — Qualidade da Água · Autor: Arthur · SENAI</small>
</footer>
</body>
</html>
