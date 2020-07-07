<h1>Sou a Danfe</h1>


{!!Form::open(array('url'=>'/api/nfe','method'=>'POST','id'=>'form','autocomplete'=>'off'))!!}
{{Form::token()}}

<p><button type="submit">Enviar</button></p>

{!!Form::close()!!}


<a href="{{asset('nfes/nota.xml')}}">Abrir XML</a>


