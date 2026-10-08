# Laboratório Digital — Qualidade da Água

Aplicação web em PHP que simula um laboratório digital de testes de qualidade
da água: classifica parâmetros de potabilidade, emite um parecer geral da
amostra e calcula a eficiência de um biofiltro experimental (antes × depois).

**Autor:** Arthur — SENAI (trabalho interárea)

## Tecnologias

- PHP >= 8.4 (requisito do `composer.json`; testado localmente no PHP 8.3.33 —
  validação final em PHP 8.4 pendente, pois não há PHP 8.4 instalado neste computador)
- HTML5 + CSS3 + Bootstrap 5 (somente CSS, sem JavaScript)
- Composer (autoload) + PHPUnit 12 (testes)
- Sem banco de dados, sem login, sem Node.js

## Estrutura

```text
qualidade-agua/
├── Controller/
│   ├── AguaController.php       # classificação dos parâmetros + parecer geral
│   └── BiofiltroController.php  # eficiência antes/depois do biofiltro
├── View/
│   ├── formulario.php           # formulários (amostra + biofiltro)
│   └── resultado.php            # tabelas de resultado e parecer
├── templates/css/style.css
├── tests/
│   ├── AguaTest.php
│   └── BiofiltroTest.php
├── docs/
│   ├── relatorio-tecnico.docx
│   └── evidencias/testes.txt
├── index.php
├── composer.json
└── phpunit.xml
```

## Instalação

```bash
cd qualidade-agua
composer install
```

## Execução com Laravel Herd

1. Aponte o Herd para a pasta `qualidade-agua/` (ou sirva com `php -S localhost:8000`);
2. Acesse `index.php` no navegador;
3. Preencha o formulário da amostra e clique em **Analisar amostra**;
4. Preencha a tabela antes/depois e clique em **Calcular eficiência**.

## Funcionamento

```text
formulário (POST) → index.php → Controller → algoritmo → View (resultado)
```

## Algoritmos

- `AguaController`: classifica pH (6,0–9,5), turbidez (≤ 5 uT),
  cloro residual (0,2–5,0 mg/L), dureza (≤ 500 mg/L CaCO₃),
  cor aparente (≤ 15 uH) e temperatura (informativa).
  Faixas conforme a **Portaria GM/MS nº 888/2021**.
  `parecerGeral()` cruza as classificações em POTÁVEL / NÃO POTÁVEL.
- `BiofiltroController`: eficiência por parâmetro com
  `((antes − depois) / antes) × 100`, tratando divisão por zero
  (retorna `null`), valores negativos (exceção) e o alerta de que
  redução de pH nem sempre é melhoria.

## Testes

```powershell
vendor\bin\phpunit
```

Cobertura: todos os métodos públicos dos dois Controllers possuem testes
(casos felizes, bordas nos limites legais, erros e integração
classificação → parecer). Resultado atual, executado no PHP 8.3.33 com PHPUnit 12.5.38: **14 testes,
55 asserções, OK** (ver `docs/evidencias/testes.txt`). Não foi possível gerar
o relatório percentual de cobertura porque o ambiente não possui Xdebug/PCOV.

## Decisões importantes

- Sem `Model/`: não há persistência; Controller + View bastam.
- Temperatura nunca reprova, pois a Portaria não define limite legal.
- As faixas seguem a Portaria GM/MS nº 888/2021 (fonte oficial), registrada
  no relatório. Nenhum dado experimental é apresentado como real: o sistema
  só exibe o que o usuário digitar, e as medições reais do biofiltro deverão
  ser inseridas nos formulários quando forem coletadas.
