<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coauthor;


class CoauthorController extends Controller
{
    public function index()
    {
        // traer todos los coautores del usuario logueado
        $coauthors = Coauthor::where('created_by', auth()->user()->id)->get();
        
        return view('coauthors')->with('coauthors', $coauthors);
    }

    public function store(Request $request)
    {
        //validaciones
        $request->validate([
            'name' => 'required',
            'surname' => 'required',
            'last_name' => 'required',
            'email' => 'required | email',
            'phone' => 'required',
            'address' => 'required',
            'institution' => 'required',
            'country' => 'required',
            'ORCID' => 'required',
            
        ]);

        //crea un nuevo coautor y lo guarda en la base de datos
        $coauthor = new Coauthor;
        $coauthor->name = $request->name;
        $coauthor->surname = $request->surname;
        $coauthor->last_name = $request->last_name;
        $coauthor->email = $request->email;
        $coauthor->phone = $request->phone;
        $coauthor->address = $request->address;
        $coauthor->institution = $request->institution;
        $coauthor->country = $request->country;
        $coauthor->ORCID = $request->ORCID;
        $coauthor->scopus_id = $request->scopus_id;
        $coauthor->researcher_id = $request->researcher_id;
        $coauthor->author_id = $request->author_id;
        $coauthor->url = $request->url;
        $coauthor->affiliation = $request->affiliation;
        $coauthor->affiliation_url = $request->affiliation_url;
        $coauthor->created_by = auth()->user()->id;

        $coauthor->save();

        return redirect()->route('coauthors');
    }

    public function edit(Coauthor $coauthor)
    {
        $coauthors = Coauthor::where('created_by', auth()->user()->id)->get();
        return view('edit-coauthors', ['coauthor' => $coauthor], ['coauthors' => $coauthors]);
    }

    public function update(Request $request, Coauthor $coauthor)
    {
        $coauthor->update([
            'name' => $request->name,
            'surname' => $request->surname,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'institution' => $request->institution,
            'country' => $request->country,
            'ORCID' => $request->ORCID,
            'scopus_id' => $request->scopus_id,
            'researcher_id' => $request->researcher_id,
            'author_id' => $request->author_id,
            'url' => $request->url,
            'affiliation' => $request->affiliation,
            'affiliation_url' => $request->affiliation_url,
        ]);

        return redirect()->route('coauthors');
    }

    public function destroy(Coauthor $coauthor)
    {
        $coauthor->delete();
        return redirect()->route('coauthors');
    }


}
