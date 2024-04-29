<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coauthor;


class CoauthorController extends Controller
{

    // Función para proteger las rutas
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    // Función para mostrar todos los coautores
    public function index()
    {
        // traer todos los coautores del usuario logueado
        $coauthors = Coauthor::where('created_by', auth()->user()->id)->get();

        $title = 'Borrar coautor';
        $text = "¿Estás seguro de que quieres borrar este coautor?";
        confirmDelete($title, $text);
        
        return view('coauthors')->with('coauthors', $coauthors);
    }

    // Función para guardar un coautor
    public function store(Request $request)
    {
        //validaciones
        $request->validate([
            'name' => 'required',
            'father_surname' => 'required',
            'mother_surname' => 'required',
            'email' => 'required | email',
            'phone' => 'required',
            'address' => 'required',
            'institution' => 'required',
            'institution_address' => 'required',
            'country' => 'required',
            'orcid' => 'required',
        ]);

        //crea un nuevo coautor y lo guarda en la base de datos
        $coauthor = new Coauthor;
        $coauthor->name = $request->name;
        $coauthor->father_surname = $request->father_surname;
        $coauthor->mother_surname = $request->mother_surname;
        $coauthor->email = $request->email;
        $coauthor->phone = $request->phone;
        $coauthor->address = $request->address;
        $coauthor->institution = $request->institution;
        $coauthor->institution_address = $request->institution_address;
        $coauthor->country = $request->country;
        $coauthor->orcid = $request->orcid;
        // $coauthor->scopus_id = $request->scopus_id;
        // $coauthor->researcher_id = $request->researcher_id;
        // $coauthor->url = $request->url;
        $coauthor->affiliation = $request->affiliation;
        $coauthor->affiliation_url = $request->affiliation_url;
        // $coauthor->biography = $request->biography;
        $coauthor->created_by = auth()->user()->id;

        $coauthor->save();

        return redirect()->route('coauthors')->with('success', 'Coauthor creado correctamente');
    }

    // Función para mostrar la vista de edición de un coautor
    public function edit(Coauthor $coauthor)
    {
        $coauthors = Coauthor::where('created_by', auth()->user()->id)->get();

        // dd($coauthor->orcid);
        return view('edit-coauthors', ['coauthor' => $coauthor], ['coauthors' => $coauthors]);
    }

    // Función para actualizar un coautor
    public function update(Request $request, Coauthor $coauthor)
    {
        //validaciones
        $request->validate([
            'name' => 'required',
            'father_surname' => 'required',
            'mother_surname' => 'required',
            'email' => 'required | email',
            'phone' => ['required', 'string', 'max:10'],
            'address' => 'required',
            'institution' => 'required',
            'institution_address' => 'required',
            'country' => 'required',
            'orcid' => ['nullable', 'string', 'unique:users', 'max:20', 'min:16', 'regex:/[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{4}/'],
        ]);

        $coauthor->update([
            'name' => $request->name,
            'father_surname' => $request->father_surname,
            'mother_surname' => $request->mother_surname,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'institution' => $request->institution,
            'institution_address' => $request->institution_address,
            'country' => $request->country,
            'orcid' => $request->orcid,
            // 'scopus_id' => $request->scopus_id,
            // 'researcher_id' => $request->researcher_id,
            // 'author_id' => $request->author_id,
            // 'url' => $request->url,
            'affiliation' => $request->affiliation,
            'affiliation_url' => $request->affiliation_url,
            // 'biography' => $request->biography,
        ]);

        return redirect()->route('coauthors')->with('success', 'Coauthor actualizado correctamente');
    }

    // Función para eliminar un coautor
    public function destroy(Coauthor $coauthor)
    {
        $coauthor->delete();
        return redirect()->route('coauthors')->with('success', 'Coauthor eliminado correctamente');
    }


}
