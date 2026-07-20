<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Mobile Money</title>
    <!-- Bootstrap 5 pour un design rapide et propre -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            /* Couleur Verte Mobile Money */
            background-color: #008751; 
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .login-header h3 {
            font-weight: bold;
            margin: 0;
        }
        .btn-yellow {
            /* Couleur Jaune Mobile Money */
            background-color: #FFD100;
            color: #000;
            font-weight: bold;
            border: none;
            transition: 0.3s;
        }
        .btn-yellow:hover {
            background-color: #e5bc00;
            color: #000;
        }
    </style>
</head>
<body>

    <div class="card login-card">
        <div class="login-header">
            <h3>MOBILE MONEY</h3>
            <p class="mb-0 text-light mt-1">Espace Client</p>
        </div>
        <div class="card-body p-4">
            
            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <form action="<?= base_url('login') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label for="telephone" class="form-label text-muted fw-semibold">Numéro de téléphone</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-success fw-bold">+261</span>
                        <input type="text" class="form-control" id="telephone" name="telephone" placeholder="033 00 000 00" required autofocus>
                    </div>
                    <div class="form-text">Entrez simplement votre numéro pour vous connecter ou créer un compte automatiquement.</div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-yellow btn-lg py-2">Se connecter</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>