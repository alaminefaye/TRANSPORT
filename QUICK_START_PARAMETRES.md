# Démarrage Rapide - Paramètres de l'Application

## 🎨 Personnalisation en 3 Étapes

### 1. Accéder aux Paramètres
- Connectez-vous à l'application
- Cliquez sur **"Paramètres"** dans le menu (en bas, icône d'engrenage)

### 2. Modifier les Paramètres

#### Changer le Nom de l'Entreprise
```
Nom de l'entreprise: [Votre Nom d'Entreprise]
```

#### Changer le Logo
- Cliquez sur "Choisir un fichier"
- Sélectionnez votre logo (JPG, PNG, GIF, SVG - max 2MB)
- Recommandé : 200x200 pixels, fond transparent

#### Changer les Couleurs
**Couleur Principale** (exemple: `#ff6b35`)
- Utilisée pour : boutons, liens, menu actif

**Couleur Secondaire** (exemple: `#8592a3`)
- Utilisée pour : éléments secondaires

### 3. Sauvegarder
- Cliquez sur **"Enregistrer les paramètres"**
- Les changements sont appliqués immédiatement !

## 📋 Exemples de Couleurs Populaires

### Format
Toutes les couleurs doivent être au format hexadécimal : `#RRGGBB`

### Exemples
| Couleur | Code | Usage |
|---------|------|-------|
| Bleu Professionnel | `#0066cc` | Entreprises, services |
| Vert Écologique | `#00a86b` | Environnement, santé |
| Rouge Dynamique | `#dc3545` | Urgence, action |
| Orange Énergique | `#fd7e14` | Créativité, jeunesse |
| Violet Moderne | `#6f42c1` | Technologie, innovation |

## 🔧 Dépannage Rapide

**Le logo ne s'affiche pas ?**
```bash
php artisan storage:link
```

**Les couleurs ne changent pas ?**
- Rafraîchissez la page (Ctrl+F5 ou Cmd+Shift+R)

**Réinitialiser les paramètres par défaut ?**
```bash
php artisan db:seed --class=SettingsSeeder --force
```

## 💡 Conseils

1. **Logo** : Utilisez un fond transparent (PNG) pour un meilleur rendu
2. **Couleurs** : Testez l'aperçu avant de sauvegarder
3. **Contraste** : Assurez-vous que le texte reste lisible
4. **Cohérence** : Utilisez les couleurs de votre charte graphique

## 📚 Documentation Complète

Pour plus de détails, consultez `PARAMETRES_GUIDE.md`

