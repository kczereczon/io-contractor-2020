<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractorStoreRequest;
use App\Http\Requests\ContractorUpdateRequest;
use App\Models\Contractor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ContractorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /** @var Builder|Contractor $contractors */
        $contractors = new Contractor();

        if (!empty($request->name)) {
            $contractors = $contractors->where('name', 'LIKE', $request->name . "%");
        }

        if (!empty($request->nip)) {
            $contractors = $contractors->where('NIP', 'LIKE', $request->nip . "%");
        }
        

        return response()->json($contractors->paginate(15), 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('contractors/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ContractorStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContractorStoreRequest $request)
    {
        $input = $request->all();
        $input['contractor']['join_date'] = now();

        $contractor = Contractor::create($input['contractor']);
        $departament = $contractor->departaments()->create(array_merge($input['departament'], ['is_main' => true]));
        if(!$departament) {
            $contractor->delete();
        }
        $contact = $departament->contacts()->create($input['contact']);
        if(!$contact) {
            $departament->delete();
            $contractor->delete();
        }

        return response()->json(Contractor::where('id',$contractor->id)->with(['departaments', 'departaments.contacts'])->first(), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  Contractor $contractor
     * @return \Illuminate\Http\Response
     */
    public function show(Contractor $contractor)
    {
        $contractor = Contractor::where('id', $contractor->id)->with(['departaments', 'departaments.contacts'])->first();
        return response()->json($contractor, $contractor ? 200 : 404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Contractor  $contractor
     * @return \Illuminate\Http\Response
     */
    public function edit(Contractor $contractor)
    {
        // return view('contractors/edit', ['contractor' => $contractor]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Contractor  $contractor
     * @return \Illuminate\Http\Response
     */
    public function update(ContractorUpdateRequest $request, Contractor $contractor)
    {
        $input = $request->all();
        $contractor = $contractor->update($input);

        return response()->json($contractor);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Contractor  $contractor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contractor $contractor)
    {
        $delete = $contractor->delete();

        return response()->json($delete, $delete ? 200 : 500);
    }
}
