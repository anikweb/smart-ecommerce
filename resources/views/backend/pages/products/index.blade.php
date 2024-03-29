@extends('backend.master')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                      <li class="breadcrumb-item active">Products</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <div class="content">
       <div class="row">
           <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Products</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="product_table"  class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Thumbnail</th>
                                        <th>Category</th>
                                        <th>Subcategory</th>
                                        <th>Created</th>
                                        <th>Last Update</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $product)
                                        <tr>
                                            <td>{{ $loop->index +1 }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td><img width="120px" class="image-responsive rounded" src="{{ asset('assets/images/product/'.$product->created_at->format('Y/m/d/').$product->id.'/thumbnail/'.$product->thumbnail) }}" alt="{{ $product->name }}"></td>
                                            <td>{{ $product->category->name }}</td>
                                            <td>{{ $product->subcategory->name }}</td>
                                            <td>{{ $product->created_at->format('d-M-Y, h:i:s A') }}</td>
                                            <td>{{ $product->updated_at->diffForHumans() }}</td>
                                            <td>
                                                @can('product edit')
                                                    <a href="{{ route('product.edit',$product->slug) }}" class=" mt-1 btn btn-primary text-center"><i class="fa fa-edit"></i> Edit</a>
                                                     <a href="{{ route('products.attribute.index',$product->slug) }}" class=" mt-1 btn btn-warning text-center"><i class="fa fa-book"></i> Attribute </a>
                                                @endcan
                                                <a href="{{ route('product.show',$product->slug) }}" class=" mt-1 btn btn-info text-center"><i class="fa fa-eye"></i> Details</a>
                                            </td>
                                        </tr>
                                    @empty
                                    <tr class="text-center text-muted">
                                        <td colspan="10"><i class="fa fa-exclamation-circle"></i> No Product to show</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
           </div>
       </div>
    </div>
@endsection
@section('footer_js')
    <script>
        // Notification
        @if (session('success'))
            toastr["success"]("{{ session('success') }}");
        @elseif(session('error'))
            toastr["error"]("{{ session('error') }}");
        @endif
        $(document).ready( function () {
            $('#product_table').DataTable();
        } );
        // $('#').DataTable();
    </script>
@endsection
