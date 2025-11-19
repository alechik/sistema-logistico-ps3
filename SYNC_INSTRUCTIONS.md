# Instrucciones para Sincronizar desde Otro Repositorio

Este script te permite traer cambios de otro repositorio (donde trabajas con otras personas) y aplicarlos como commits propios, manteniendo tu historial personal.

## Uso Básico

```powershell
.\sync-from-other-repo.ps1 -OtherRepoUrl "https://github.com/otro-usuario/otro-repo.git"
```

## Parámetros Opcionales

- `-Branch`: La rama del otro repositorio (por defecto: "main")
- `-RemoteName`: Nombre del remoto (por defecto: "other-repo")
- `-CommitMessage`: Mensaje personalizado para el commit (opcional)

## Ejemplos

### Ejemplo 1: Sincronización básica
```powershell
.\sync-from-other-repo.ps1 -OtherRepoUrl "https://github.com/colaborador/proyecto.git"
```

### Ejemplo 2: Con mensaje personalizado
```powershell
.\sync-from-other-repo.ps1 -OtherRepoUrl "https://github.com/colaborador/proyecto.git" -CommitMessage "Integrar mejoras del equipo"
```

### Ejemplo 3: Desde otra rama
```powershell
.\sync-from-other-repo.ps1 -OtherRepoUrl "https://github.com/colaborador/proyecto.git" -Branch "develop"
```

## ¿Qué hace el script?

1. **Agrega el otro repositorio como remoto** (si no existe)
2. **Obtiene los cambios** del otro repositorio
3. **Combina todos los commits** en uno solo (squash)
4. **Crea un commit con tu autoría** (tu nombre y email)
5. **Mantiene tu historial** intacto

## Después de sincronizar

Una vez que el script termine, puedes revisar los cambios y luego hacer push:

```powershell
git push origin main
```

## Notas Importantes

- El script usa `merge --squash`, que combina todos los commits del otro repositorio en uno solo
- Todos los commits aparecerán con tu autoría
- Tu historial local se mantiene intacto
- Si hay conflictos, tendrás que resolverlos manualmente antes de hacer commit

