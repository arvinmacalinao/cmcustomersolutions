@if(isset($bom) AND count($bom) > 0)
<div class="panel panel-default">
    <div class="panel-heading">Model BOM Item(s)</div>
        <div class="panel-body">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Item Code</th>
                    <th>Category</th>
                    <th>Created By</th>
                </tr>
            </thead>
            <tbody id="device-result">
                @foreach($bom as $index => $item)
                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->code }}</td>
                    <td>{{ $item->category }}</td>
                    <td>{{ $item->creator }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif