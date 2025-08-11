<div class="flex justify-between p-2">
    @if(isset($_GET['skip']) && $_GET['skip'] != 0)
        <x-abutton href="?skip={{isset($_GET['skip']) && $_GET['skip'] != 0? $_GET['skip'] - config('app.maxRecPerPage'):config('app.maxRecPerPage')}}">Previous</x-abutton>
    @else
        <span></span>
    @endif
    <x-abutton href="?skip={{isset($_GET['skip'])? $_GET['skip']+config('app.maxRecPerPage'):config('app.maxRecPerPage')}}">Next</x-abutton>
</div>
