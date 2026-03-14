<?php
// --- 1. CONNESSIONE ---
$connessione = new mysqli("localhost", "root", "", "gestione_spese");

// --- 2. LOGICA ELIMINA (DELETE) ---
if (isset($_GET['elimina'])) {
    $id_da_eliminare = $_GET['elimina'];
    $connessione->query("DELETE FROM spese WHERE id = $id_da_eliminare");
    header("Location: index.php"); // Ricarica la pagina per pulire l'URL
}

// --- 3. LOGICA SALVATAGGIO (CREATE) ---
$messaggio = "";
if (isset($_POST['salva_bottone'])) {
    $titolo = $connessione->real_escape_string($_POST['nome_spesa']);
    $importo = $connessione->real_escape_string($_POST['importo_spesa']);
    $categoria = "Generale";
    $sql_insert = "INSERT INTO spese (titolo, importo, categoria) VALUES ('$titolo', '$importo', '$categoria')";
    if ($connessione->query($sql_insert) === TRUE) {
        $messaggio = "✅ Spesa salvata!";
    }
}

// --- 4. LETTURA DATI E CALCOLO TOTALE ---
$sql_select = "SELECT * FROM spese ORDER BY data DESC";
$risultato = $connessione->query($sql_select);

$totale_complessivo = 0; // Variabile per accumulare la somma
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestore Spese Elena Pro</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f4f8; display: flex; flex-direction: column; align-items: center; padding: 40px; }
        .scheda { background-color: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 400px; margin-bottom: 30px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        button { width: 100%; background-color: #0056b3; color: white; padding: 12px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .tabella-spese { width: 100%; max-width: 800px; background: white; border-collapse: collapse; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .tabella-spese th, .tabella-spese td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .tabella-spese th { background-color: #0056b3; color: white; }
        .riga-totale { background-color: #eef2f7; font-weight: bold; font-size: 1.1em; }
        .btn-elimina { color: #d9534f; text-decoration: none; font-weight: bold; font-size: 0.9em; }
        .btn-elimina:hover { color: #c9302c; }
    </style>
</head>
<body>

    <div class="scheda">
        <h1>Nuova Spesa</h1>
        <p style="color: green; text-align: center;"><?php echo $messaggio; ?></p>
        <form method="POST">
            <input type="text" name="nome_spesa" placeholder="Cosa hai comprato?" required>
            <input type="number" step="0.01" name="importo_spesa" placeholder="Importo (€)" required>
            <button type="submit" name="salva_bottone">Salva Spesa</button>
        </form>
    </div>

    <h2>Storico Spese</h2>
    <table class="tabella-spese">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Importo</th>
                <th>Data</th>
                <th>Azione</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($risultato->num_rows > 0) {
                while($riga = $risultato->fetch_assoc()) {
                    $totale_complessivo += $riga["importo"]; // Sommiamo ogni riga al totale
                    echo "<tr>";
                    echo "<td>" . $riga["titolo"] . "</td>";
                    echo "<td>€ " . number_format($riga["importo"], 2, ',', '.') . "</td>";
                    echo "<td>" . date('d/m/Y', strtotime($riga["data"])) . "</td>";
                    // Tasto elimina che passa l'ID nell'URL
                    echo "<td><a href='index.php?elimina=" . $riga["id"] . "' class='btn-elimina' onclick='return confirm(\"Sei sicura?\")'>Elimina</a></td>";
                    echo "</tr>";
                }
            }
            ?>
            <tr class="riga-totale">
                <td>TOTALE</td>
                <td colspan="3">€ <?php echo number_format($totale_complessivo, 2, ',', '.'); ?></td>
            </tr>
        </tbody>
    </table>

</body>
</html>