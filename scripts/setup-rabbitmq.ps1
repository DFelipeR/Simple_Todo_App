Param()

Write-Host "[Setup] Detectando RabbitMQ sbin..." -ForegroundColor Cyan
$base = 'C:\Program Files\RabbitMQ Server'
if (-not (Test-Path $base)) {
    Write-Error "RabbitMQ no encontrado en '$base'. Instálalo con Chocolatey o instalador GUI."
    exit 1
}

$rb = Get-ChildItem $base | Sort-Object Name -Descending | Select-Object -First 1
$sbin = Join-Path $rb.FullName 'sbin'
if (-not (Test-Path $sbin)) {
    Write-Error "Carpeta 'sbin' no encontrada en '$($rb.FullName)'."
    exit 1
}

Write-Host "[Setup] Usando sbin: $sbin" -ForegroundColor Green

Write-Host "[Setup] Habilitando plugin rabbitmq_management..." -ForegroundColor Cyan
& "$sbin\rabbitmq-plugins.bat" enable rabbitmq_management
if ($LASTEXITCODE -ne 0) { Write-Warning "No se pudo habilitar el plugin (puede ya estar habilitado)." }

Write-Host "[Setup] Instalando servicio..." -ForegroundColor Cyan
& "$sbin\rabbitmq-service.bat" install
Write-Host "[Setup] Iniciando servicio..." -ForegroundColor Cyan
& "$sbin\rabbitmq-service.bat" start

Write-Host "[Setup] Consultando estado..." -ForegroundColor Cyan
& "$sbin\rabbitmqctl.bat" status

Write-Host "[Setup] Panel: http://localhost:15672 (guest/guest)" -ForegroundColor Green
Write-Host "[Setup] Puerto AMQP: 5672" -ForegroundColor Green
