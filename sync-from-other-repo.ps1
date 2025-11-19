# Script para sincronizar cambios de otro repositorio
# y crear commits propios manteniendo tu historial

param(
    [Parameter(Mandatory=$true)]
    [string]$OtherRepoUrl,
    
    [Parameter(Mandatory=$false)]
    [string]$Branch = "main",
    
    [Parameter(Mandatory=$false)]
    [string]$RemoteName = "other-repo",
    
    [Parameter(Mandatory=$false)]
    [string]$CommitMessage = ""
)

Write-Host "=== Sincronización desde otro repositorio ===" -ForegroundColor Cyan

# Verificar si el remoto ya existe
$remoteExists = git remote | Select-String -Pattern "^$RemoteName$"

if (-not $remoteExists) {
    Write-Host "Agregando remoto '$RemoteName'..." -ForegroundColor Yellow
    git remote add $RemoteName $OtherRepoUrl
} else {
    Write-Host "El remoto '$RemoteName' ya existe. Actualizando URL..." -ForegroundColor Yellow
    git remote set-url $RemoteName $OtherRepoUrl
}

# Hacer fetch del otro repositorio
Write-Host "`nObteniendo cambios del otro repositorio..." -ForegroundColor Yellow
git fetch $RemoteName $Branch

# Verificar si hay cambios
$localCommit = git rev-parse HEAD
$remoteCommit = git rev-parse "$RemoteName/$Branch"

if ($localCommit -eq $remoteCommit) {
    Write-Host "`nNo hay cambios nuevos. Todo está actualizado." -ForegroundColor Green
    exit 0
}

# Mostrar commits que se van a traer
Write-Host "`nCommits que se traerán:" -ForegroundColor Cyan
git log --oneline $localCommit..$remoteCommit

# Crear un commit combinado (squash) con todos los cambios
Write-Host "`nAplicando cambios como un commit propio..." -ForegroundColor Yellow

# Guardar cambios actuales si hay alguno sin commitear
$hasChanges = git diff --quiet
if (-not $hasChanges) {
    Write-Host "Hay cambios sin commitear. Guardándolos en stash..." -ForegroundColor Yellow
    git stash push -m "Cambios temporales antes de sync"
    $stashed = $true
} else {
    $stashed = $false
}

# Hacer merge con squash (combina todos los commits en uno)
if ([string]::IsNullOrWhiteSpace($CommitMessage)) {
    $commitCount = (git rev-list --count $localCommit..$remoteCommit)
    $CommitMessage = "Sync: Integrar cambios del repositorio colaborativo ($commitCount commits combinados)"
}

Write-Host "`nCreando commit con mensaje: $CommitMessage" -ForegroundColor Cyan
git merge --squash "$RemoteName/$Branch" --no-edit

# Configurar el commit con tu autoría
$userName = git config user.name
$userEmail = git config user.email

Write-Host "`nCreando commit con tu autoría..." -ForegroundColor Yellow
git commit --author="$userName <$userEmail>" -m $CommitMessage

# Restaurar cambios si se guardaron
if ($stashed) {
    Write-Host "`nRestaurando cambios guardados..." -ForegroundColor Yellow
    git stash pop
}

Write-Host "`n=== Sincronización completada ===" -ForegroundColor Green
Write-Host "Los cambios han sido aplicados como un commit propio." -ForegroundColor Green
Write-Host "Puedes hacer push cuando estés listo: git push origin main" -ForegroundColor Cyan

