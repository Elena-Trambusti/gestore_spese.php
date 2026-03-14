<?php
// Immagina questa funzione come un "Contabile" a cui passi una lista
function calcolaTotale($lista_spese) {
    $somma = 0; // Partiamo da zero
    
    if ($lista_spese->num_rows > 0) {
        // 1. Torniamo all'inizio della lista (per sicurezza)
        $lista_spese->data_seek(0); 
        
        // 2. Scorriamo la lista e sommiamo ogni importo
        while ($riga = $lista_spese->fetch_assoc()) {
            $somma += $riga['importo'];
        }
        
        // 3. Molto importante: riportiamo la lista all'inizio 
        // così la tabella può rileggerla per mostrarla a video
        $lista_spese->data_seek(0);
    }
    
    return $somma; // Restituiamo il risultato finale
}
?>