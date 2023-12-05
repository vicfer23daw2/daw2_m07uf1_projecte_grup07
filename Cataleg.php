<?php

// Interfície per als productes
interface ProducteInterface {
    public function mostrarInfo();
}

// Classe base per als productes
class Producte implements ProducteInterface {
    protected $id;
    protected $nom;
    protected $preu;
    protected $iva;
    protected $disponibilitat;

    public function __construct($id, $nom, $preu, $iva, $disponibilitat) {
        $this->id = $id;
        $this->nom = $nom;
        $this->preu = $preu;
        $this->iva = $iva;
        $this->disponibilitat = $disponibilitat;
    }

    public function mostrarInfo() {
        echo "ID: {$this->id}, Nom: {$this->nom}, Preu: {$this->preu}€, IVA: {$this->iva}%, Disponibilitat: {$this->disponibilitat}\n";
    }

    // Mètodes per obtenir atributs
    public function getId() {
        return $this->id;
    }

    public function getNom() {
        return $this->nom;
    }

    public function getPreu() {
        return $this->preu;
    }

    public function getIVA() {
        return $this->iva;
    }

    public function getDisponibilitat() {
        return $this->disponibilitat;
    }
}

// Classe per a la gestió del catàleg
class Cataleg {
    protected $productes;

    public function __construct() {
        $this->productes = [];
        $this->carregarProductes();
    }

    public function getProductes() {
        return $this->productes;
    }

    public function afegirProducte(Producte $producte) {
        $this->productes[] = $producte;
        $this->actualitzarCataleg();
        return true;
    }

    public function esborrarProducte($id) {
        foreach ($this->productes as $key => $producte) {
            if ($producte->getId() === $id) {
                unset($this->productes[$key]);
                $this->actualitzarCataleg();
                return true;
            }
        }
        return false;
    }

    public function modificarProducte($id, $nouNom, $nouPreu, $nouIVA, $novaDisponibilitat) {
        foreach ($this->productes as $key => $producte) {
            if ($producte->getId() === $id) {
                // Eliminem el producte amb l'ID proporcionat
                unset($this->productes[$key]);

                // Creem un nou producte amb les dades del formulari
                $producteModificat = new Producte(
                    $id,
                    $nouNom,
                    floatval($nouPreu),
                    floatval($nouIVA),
                    $novaDisponibilitat
                );

                // Afegim el nou producte al catàleg
                $this->productes[] = $producteModificat;

                // Actualitzem el catàleg
                $this->actualitzarCataleg();

                return true;
            }
        }

        return false;
    }


    public function getProducteById($id) {
        foreach ($this->productes as $producte) {
            if ($producte->getId() == $id) {
                return $producte;
            }
        }
        return null; // Retorna null si no es troba cap producte amb l'ID proporcionat
    }

    public function mostrarProductesOrdenats() {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Nom</th><th>Preu</th><th>IVA</th><th>Disponibilitat</th></tr>";
    
        usort($this->productes, function ($a, $b) {
            return strcmp($a->getNom(), $b->getNom());
        });
    
        foreach ($this->productes as $producte) {
            echo "<tr>";
            echo "<td>{$producte->getId()}</td>";
            echo "<td>{$producte->getNom()}</td>";
            echo "<td>{$producte->getPreu()}€</td>";
            echo "<td>{$producte->getIVA()}%</td>";
            echo "<td>{$producte->getDisponibilitat()}</td>";
            echo "</tr>";
        }
    
        echo "</table>";
    }
    
    protected function actualitzarCataleg() {
        // Actualitza l'arxiu cataleg amb la informació dels productes
        $catalegText = '';
        foreach ($this->productes as $producte) {
            $catalegText .= "{$producte->getId()}:{$producte->getNom()}:{$producte->getPreu()}:{$producte->getIVA()}:{$producte->getDisponibilitat()}\n";
        }
        file_put_contents('./dades/cataleg', $catalegText);
    }

    protected function carregarProductes() {
        // Carrega les dades del fitxer cataleg a l'array de productes
        $catalegText = file_get_contents('./dades/cataleg');
        $linies = explode("\n", $catalegText);
        foreach ($linies as $linia) {
            $dadesProducte = explode(":", $linia);
            if (count($dadesProducte) === 5) {
                $producte = new Producte(
                    intval($dadesProducte[0]),
                    $dadesProducte[1],
                    floatval($dadesProducte[2]),
                    floatval($dadesProducte[3]),
                    $dadesProducte[4]
                );
                $this->productes[] = $producte;
            }
        }
    }
}

?>
