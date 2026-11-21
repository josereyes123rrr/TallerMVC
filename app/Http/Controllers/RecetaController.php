<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RecetaController extends Controller
{
    public const TIPOS = ['Desayuno', 'Almuerzo', 'Cena', 'Postre'];

    private function cargar(): array
    {
        if (! session()->has('recetas')) {
            session(['recetas' => $this->precargadas()]);
        }
        return session('recetas');
    }

    private function precargadas(): array
    {
        return [
            ['id' => 'base1', 'nombre' => 'Huevos pericos', 'tipo' => 'Desayuno', 'ingredientes' => "4 huevos\n2 tomates\n2 cebollas largas\n1 cda de mantequilla\nSal", 'preparacion' => 'Sofríe tomate y cebolla en mantequilla, agrega los huevos batidos y revuelve hasta que cuajen.', 'favorita' => false, 'propia' => false],
            ['id' => 'base2', 'nombre' => 'Arepa con queso', 'tipo' => 'Desayuno', 'ingredientes' => "2 tazas de harina de maíz precocida\n2 tazas de agua tibia\n1 cdta de sal\nQueso mozzarella", 'preparacion' => 'Mezcla harina, agua y sal, forma las arepas, ásalas por ambos lados y rellénalas con queso.', 'favorita' => false, 'propia' => false],
            ['id' => 'base3', 'nombre' => 'Arroz con pollo', 'tipo' => 'Almuerzo', 'ingredientes' => "2 tazas de arroz\n500 g de pollo\n1 zanahoria\n1/2 taza de arvejas\nCebolla, ajo y comino", 'preparacion' => 'Sofríe pollo con el guiso, agrega arroz, verduras y caldo; cocina tapado a fuego bajo 20 minutos.', 'favorita' => false, 'propia' => false],
            ['id' => 'base4', 'nombre' => 'Lentejas guisadas', 'tipo' => 'Almuerzo', 'ingredientes' => "1 taza de lentejas\n1 zanahoria\n1 papa\nCebolla, ajo y cilantro", 'preparacion' => 'Hierve las lentejas con las verduras picadas y el guiso hasta que estén blandas, unos 40 minutos.', 'favorita' => false, 'propia' => false],
            ['id' => 'base5', 'nombre' => 'Sopa de verduras', 'tipo' => 'Cena', 'ingredientes' => "1 zanahoria\n1 papa\n1 calabacín\nCebolla\nCaldo de verduras", 'preparacion' => 'Pica las verduras, cocínalas en el caldo 25 minutos y sazona al gusto.', 'favorita' => false, 'propia' => false],
            ['id' => 'base6', 'nombre' => 'Sándwich de pollo', 'tipo' => 'Cena', 'ingredientes' => "Pan de molde\nPechuga de pollo desmechada\nLechuga\nTomate\nMayonesa", 'preparacion' => 'Mezcla el pollo con mayonesa y arma el sándwich con lechuga y tomate.', 'favorita' => false, 'propia' => false],
            ['id' => 'base7', 'nombre' => 'Arroz con leche', 'tipo' => 'Postre', 'ingredientes' => "1 taza de arroz\n4 tazas de leche\n1 taza de azúcar\nCanela\nPasas", 'preparacion' => 'Cocina el arroz en agua, agrega leche, azúcar y canela y cocina a fuego bajo hasta espesar.', 'favorita' => false, 'propia' => false],
            ['id' => 'base8', 'nombre' => 'Brownie de chocolate', 'tipo' => 'Postre', 'ingredientes' => "200 g de chocolate\n100 g de mantequilla\n3 huevos\n1 taza de azúcar\n1/2 taza de harina", 'preparacion' => 'Derrite chocolate y mantequilla, mezcla con huevos, azúcar y harina y hornea 25 minutos a 180 °C.', 'favorita' => false, 'propia' => false],
        ];
    }

    public function index(Request $request)
    {
        $recetas = $this->cargar();
        $tipo = (string) $request->query('tipo', '');
        $q = trim((string) $request->query('q', ''));
        $soloFavoritas = $request->boolean('favoritas');

        $filtradas = array_values(array_filter($recetas, function ($r) use ($tipo, $q, $soloFavoritas) {
            if ($tipo !== '' && $r['tipo'] !== $tipo) {
                return false;
            }
            if ($soloFavoritas && ! $r['favorita']) {
                return false;
            }
            if ($q !== '' && mb_stripos($r['nombre'] . ' ' . $r['ingredientes'], $q) === false) {
                return false;
            }
            return true;
        }));

        return view('ejercicios.recetas', [
            'recetas' => $filtradas,
            'tipos' => self::TIPOS,
            'tipoSel' => $tipo,
            'q' => $q,
            'soloFavoritas' => $soloFavoritas,
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
            'tipo' => 'required|in:' . implode(',', self::TIPOS),
            'ingredientes' => 'required|string|max:1500',
            'preparacion' => 'required|string|max:3000',
        ]);

        $recetas = $this->cargar();
        $recetas[] = [
            'id' => uniqid(),
            'nombre' => trim($datos['nombre']),
            'tipo' => $datos['tipo'],
            'ingredientes' => trim($datos['ingredientes']),
            'preparacion' => trim($datos['preparacion']),
            'favorita' => false,
            'propia' => true,
        ];
        session(['recetas' => $recetas]);

        return redirect()->route('recetas.index')->with('ok', 'Receta publicada.');
    }

    public function favorita(string $id)
    {
        $recetas = array_map(function ($r) use ($id) {
            if ($r['id'] === $id) {
                $r['favorita'] = ! $r['favorita'];
            }
            return $r;
        }, $this->cargar());
        session(['recetas' => $recetas]);

        return back();
    }

    public function destroy(string $id)
    {
        $recetas = array_values(array_filter($this->cargar(), fn ($r) => ! ($r['id'] === $id && $r['propia'])));
        session(['recetas' => $recetas]);

        return redirect()->route('recetas.index')->with('ok', 'Receta eliminada.');
    }
}
