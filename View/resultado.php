<?php if (!empty($erros)): ?>
    <div class="alert alert-danger">
        <strong>Verifique os dados:</strong>
        <ul class="mb-0">
            <?php foreach ($erros as $erro): ?>
                <li><?= htmlspecialchars($erro) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (!empty($analise) && ($analise['valida'] ?? false)): ?>
    <div class="card mb-4">
        <div class="card-header">Resultado da análise</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr><th>Parâmetro</th><th>Valor</th><th>Status</th><th>Comentário</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($analise['parametros'] as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['parametro']) ?></td>
                            <td><?= htmlspecialchars((string) $p['valor']) ?></td>
                            <td>
                                <span class="badge <?= $p['status'] === 'dentro do padrão' ? 'bg-success' : ($p['status'] === 'informativo' ? 'bg-info' : 'bg-danger') ?>">
                                    <?= htmlspecialchars($p['status']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($p['mensagem']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="alert <?= $analise['parecer']['situacao'] === 'POTÁVEL' ? 'alert-success' : 'alert-danger' ?>">
                <strong>Parecer geral: <?= htmlspecialchars($analise['parecer']['situacao']) ?></strong><br>
                <?= htmlspecialchars($analise['parecer']['mensagem']) ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($bio) && ($bio['valido'] ?? false)): ?>
    <div class="card mb-4">
        <div class="card-header">Eficiência do biofiltro</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr><th>Parâmetro</th><th>Antes</th><th>Depois</th><th>Eficiência (%)</th><th>Avaliação</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bio['comparacao'] as $linha): ?>
                        <tr>
                            <td><?= htmlspecialchars($linha['parametro']) ?></td>
                            <td><?= htmlspecialchars((string) $linha['antes']) ?></td>
                            <td><?= htmlspecialchars((string) $linha['depois']) ?></td>
                            <td><?= $linha['eficiencia'] === null ? '—' : number_format($linha['eficiencia'], 2, ',', '.') . ' %' ?></td>
                            <td>
                                <?= htmlspecialchars($linha['avaliacao']) ?>
                                <?php if ($linha['alerta']): ?><br><small class="text-muted"><?= htmlspecialchars($linha['alerta']) ?></small><?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
