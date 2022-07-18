<!DOCTYPE html>
<html>
<head>
  <title>Export picture markers</title>
</head>
<body>

@foreach($markers as $marker)
  <img class="m-5" src="{{ public_path($marker['image_marker'])}}">
  <h1 class="text-center text-black">Name: {{$marker['name']}}</h1>
  <div style="page-break-after: always;"></div>
@endforeach

</body>

</html>
