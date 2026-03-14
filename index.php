<?php
require_once 'db.php';
require_once 'functions.php'; // Stai dicendo: "Usa le ricette che ci sono qui dentro"

// --- 2. LOGICA ELIMINA (DELETE) ---
if (isset($_GET['elimina'])) {
    $id_da_eliminare = $_GET['elimina'];
    $connessione->query("DELETE FROM spese WHERE id = $id_da_eliminare");
    header("Location: index.php"); // Ricarica la pagina per pulire l'URL
}

// --- 3. LOGICA SALVATAGGIO (CREATE) ---
$messaggio = "";
if (isset($_POST['salva_bottone'])) {
    //1.Recupero dati base
    $titolo=$_POST['nome_spesa'];
    $importo=$_POST['importo_spesa'];
    $categoria = isset($_POST['categoria_spesa']) ? $_POST['categoria_spesa'] : "Altro";

    $sql = "INSERT INTO spese (titolo, importo, categoria) VALUES (?, ?, ?)";

    $stmt=$connessione->prepare($sql);
    // s = stringa, d = double (prezzo), s = stringa
        $stmt->bind_param("sds", $titolo, $importo, $categoria);

        //4.ESECUZIONE
        if($stmt->execute()){
            $messaggio="Spesa salvata in sicurezza!";
        }else{
            $messaggio="Errore durante il salvataggio: ".$stmt->error;
        }
        $stmt->close();
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
    <link rel="stylesheet"href="style.css">
</head>
<body>

    <div class="scheda">
        <h1>Nuova Spesa</h1>
        <p style="color: green; text-align: center;"><?php echo $messaggio; ?></p>
        <form method="POST">
            <input type="text" name="nome_spesa" placeholder="Cosa hai comprato?" required>
            <input type="number" step="0.01" name="importo_spesa" placeholder="Importo (€)" required>
            <select name="categoria_spesa" required>
    <option value="" disabled selected>Scegli una categoria...</option>
    <option value="Alimentari">Alimentari 🍎</option>
    <option value="Trasporti">Trasporti 🚗</option>
    <option value="Svago">Svago 🍕</option>
    <option value="Bollette">Bollette 💡</option>
    <option value="Altro">Altro ✨</option>
</select>
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
    <td colspan="3" style="text-align: right;"><strong>TOTALE COMPLESSIVO:</strong></td>
    <td><strong>€ <?php echo number_format(calcolaTotale($risultato), 2, ',', '.'); ?></strong></td>
</tr>
        </tbody>
    </table>

</body>
</html>
