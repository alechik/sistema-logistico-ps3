# Proceso Manual de Sincronización

Si prefieres hacerlo manualmente, aquí están los pasos:

## Paso 1: Agregar el otro repositorio como remoto

```powershell
git remote add other-repo https://github.com/otro-usuario/otro-repo.git
```

(Solo la primera vez. Si ya existe, omite este paso)

## Paso 2: Obtener los cambios

```powershell
git fetch other-repo main
```

## Paso 3: Hacer merge con squash (combina commits en uno solo)

```powershell
git merge --squash other-repo/main
```

## Paso 4: Crear el commit con tu autoría

```powershell
git commit -m "Sync: Integrar cambios del repositorio colaborativo"
```

O con mensaje personalizado:

```powershell
git commit -m "Tu mensaje personalizado aquí"
```

## Paso 5: Subir los cambios

```powershell
git push origin main
```

## Ver qué commits se van a traer (antes de hacer merge)

```powershell
git log HEAD..other-repo/main --oneline
```

