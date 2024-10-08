<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $search = request('search');

        if ($search) {
            $events = Event::where([['title', 'like', '%' . $search . '%']])->get();
        } else {
            $events = Event::all();
        }


        return view('welcome', ['events' => $events, 'search' => $search]);
    }

    public function create()
    {
        return view('events.create');
    }

    //Envia dados ao banco de dados
    public function store(Request $request)
    {

        $event = new Event;

        $event->title       = $request->title;
        $event->date        = $request->date;
        $event->city        = $request->city;
        $event->private     = $request->private;
        $event->description = $request->description;
        $event->items       = $request->items;

        // Image Upload
        if ($request->hasFile('image') && $request->file('image')->isValid()) {

            $requestImage   = $request->image;
            $extension      = $requestImage->extension();
            $imageName      = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;
            $requestImage->move(public_path('img/events'), $imageName);

            $event->image   = $imageName;
        }

        // Salva todos os requests acima no banco de dados
        $event->save();

        //Redireciona para a raiz "/" e envia uma mensagem "msg"
        return redirect('/')->with('msg', 'Evento criado com sucesso!');
    }

    //Mostra os dados que EventController salvou
    public function show($id)
    {
        $event = Event::findOrFail($id);

        return view('events.show', ['event' => $event]);
    }
}
