@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">產品</div>

                    <div class="card-body">

                        <!-- 新增商品的表單 -->
                        <div id="create-product-form" style="display: none;">
                            <h2>新增商品</h2>
                            <form>
                                <div class="form-group">
                                    <label for="name">商品名稱</label>
                                    <input type="text" id="name" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="price">價格</label>
                                    <input type="number" id="price" class="form-control">
                                </div>
                                <button type="button" id="submit" class="btn btn-primary">送出</button>
                            </form>
                        </div>

                        <!-- 新增商品的按鈕 -->
                        <button id="create-product-button" class="btn btn-primary">新增商品</button>

                        <table class="table mt-4">
                            <thead>
                                <tr>
                                    <th>名稱</th>
                                    <th>價格</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->price }}</td>
                                        <td>
                                            <button class="btn btn-secondary edit-button"
                                                data-id="{{ $product->id }}">編輯</button>
                                            <button class="btn btn-danger delete-button"
                                                data-id="{{ $product->id }}">刪除</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // 當點選「新增商品」按鈕時，顯示表單
        $('#create-product-button').click(function() {
            $('#create-product-form').show();
        });

        // 當點選「送出」按鈕時，呼叫 API
        $('#submit').click(function() {
            $.ajax({
                url: '/api/products',
                type: 'POST',
                data: {
                    name: $('#name').val(),
                    price: $('#price').val()
                },
                success: function(data) {
                    alert('商品已新增');
                    $('#create-product-form').hide();
                }
            });
        });

        // 當點選「編輯」按鈕時，呼叫 API 並顯示編輯表單
        $('.edit-button').click(function() {
            var id = $(this).data('id');
            $.ajax({
                url: '/api/products/' + id,
                type: 'GET',
                success: function(data) {
                    $('#edit-name').val(data.name);
                    $('#edit-price').val(data.price);
                    $('#edit-product-form').show();
                }
            });
        });

        // 當點選「刪除」按鈕時，呼叫 API 並刪除商品
        $('.delete-button').click(function() {
            var id = $(this).data('id');
            $.ajax({
                url: '/api/products/' + id,
                type: 'DELETE',
                success: function(data) {
                    alert('商品已刪除');
                    location.reload();
                }
            });
        });
    });
</script>
