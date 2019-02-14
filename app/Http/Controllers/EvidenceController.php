<?php

namespace App\Http\Controllers;

use App\Evidence;
use App\Source;
use App\Insight;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvidenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Source $source, Insight $insight)
    {
        $evidence = Evidence::where([
            ['user_id', Auth::id()],
            ['insight_id', $insight->id]
        ])->orderBy('created_at', 'desc')
        ->get();

        return view('evidence.create')->with([
            'source' => $source,
            'insight' => $insight,
            'evidence' => $evidence,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Source $source, Insight $insight)
    {
        $validator = Validator::make($request->all(), [
            'quote' => 'required|max:1024',
            'location' => 'required|max:255',
            'finish' => 'required',
        ]);

        if($validator->fails()) {
            return redirect('/source/' . $source->id . '/insight/' . $insight->id . '/evidence/create')
                    ->withErrors($validator)
                    ->withInput();
        }

        $evidence = Evidence::create([
            'user_id' => Auth::id(),
            'insight_id' => $insight->id,
            'quote' => $request->quote,
            'location' => $request->location,
        ]);

        if($request->finish == "true") {
            return redirect('/source/' . $source->id . '/insight/' . $insight->id . '/show');
        }

        return redirect('/source/' . $source->id . '/insight/' . $insight->id . '/evidence/create');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Evidence  $evidence
     * @return \Illuminate\Http\Response
     */
    public function show(Source $source, Insight $insight, Evidence $evidence)
    {
        return view('evidence.show')->with([
            'source' => $source,
            'insight' => $insight,
            'evidence' => $evidence,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Evidence  $evidence
     * @return \Illuminate\Http\Response
     */
    public function edit(Source $source, Insight $insight, Evidence $evidence)
    {
        $allEvidence = Evidence::where([
            ['user_id', Auth::id()],
            ['insight_id', $insight->id]
        ])->orderBy('created_at', 'desc')
        ->get();

        return view('evidence.edit')->with([
            'source' => $source,
            'insight' => $insight,
            'evidence' => $allEvidence,
            'evidenceEdit' => $evidence,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Evidence  $evidence
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Source $source, Insight $insight, Evidence $evidence)
    {
        $validator = Validator::make($request->all(), [
            'quote' => 'required|max:1024',
            'location' => 'required|max:255',
        ]);

        if($validator->fails()) {
            return redirect('/source/' . $source->id . '/insight/' . $insight->id . '/evidence/' . $evidence->id . '/create')
                    ->withErrors($validator)
                    ->withInput();
        }

        $evidence->update([
            'user_id' => Auth::id(),
            'insight_id' => $insight->id,
            'quote' => $request->quote,
            'location' => $request->location,
        ]);

        return redirect('/source/' . $source->id . '/insight/' . $insight->id . '/evidence/' . $evidence->id . '/show');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Evidence  $evidence
     * @return \Illuminate\Http\Response
     */
    public function destroy(Source $source, Insight $insight, Evidence $evidence)
    {
        $evidence->delete();

        return redirect('/source/' . $source->id . '/insight/' . $insight->id . '/show');
    }
}
