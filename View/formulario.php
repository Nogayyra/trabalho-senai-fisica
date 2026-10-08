<div class="card mb-4">
    <div class="card-header">1. Análise da amostra</div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="acao" value="analisar">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label" for="ph">pH (0 a 14)</label>
                    <input class="form-control" type="number" step="any" id="ph" name="ph" required
                           value="<?= htmlspecialchars($_POST['ph'] ?? '') ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label" for="turbidez">Turbidez (uT)</label>
                    <input class="form-control" type="number" step="any" min="0" id="turbidez" name="turbidez" required
                           value="<?= htmlspecialchars($_POST['turbidez'] ?? '') ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label" for="cloro">Cloro residual (mg/L)</label>
                    <input class="form-control" type="number" step="any" min="0" id="cloro" name="cloro" required
                           value="<?= htmlspecialchars($_POST['cloro'] ?? '') ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label" for="dureza">Dureza (mg/L em CaCO3)</label>
                    <input class="form-control" type="number" step="any" min="0" id="dureza" name="dureza" required
                           value="<?= htmlspecialchars($_POST['dureza'] ?? '') ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label" for="cor">Cor aparente (uH)</label>
                    <input class="form-control" type="number" step="any" min="0" id="cor" name="cor" required
                           value="<?= htmlspecialchars($_POST['cor'] ?? '') ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label" for="temperatura">Temperatura (°C)</label>
                    <input class="form-control" type="number" step="any" id="temperatura" name="temperatura" required
                           value="<?= htmlspecialchars($_POST['temperatura'] ?? '') ?>">
                </div>
            </div>
            <button class="btn btn-primary" type="submit">Analisar amostra</button>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">2. Efeito do biofiltro (antes × depois)</div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="acao" value="biofiltro">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Parâmetro</th>
                            <th>Antes</th>
                            <th>Depois</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (['turbidez' => 'Turbidez (uT)', 'cor' => 'Cor (uH)', 'cloro' => 'Cloro (mg/L)', 'dureza' => 'Dureza (mg/L)', 'ph' => 'pH'] as $campo => $rotulo): ?>
                        <tr>
                            <td><?= $rotulo ?></td>
                            <td><input class="form-control" type="number" step="any" min="0" name="antes[<?= $campo ?>]" required
                                       value="<?= htmlspecialchars($_POST['antes'][$campo] ?? '') ?>"></td>
                            <td><input class="form-control" type="number" step="any" min="0" name="depois[<?= $campo ?>]" required
                                       value="<?= htmlspecialchars($_POST['depois'][$campo] ?? '') ?>"></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <button class="btn btn-success" type="submit">Calcular eficiência</button>
        </form>
    </div>
</div>
