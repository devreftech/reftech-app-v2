<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web Maintanance</title>
    <style>
        .image-contruct {
            width: 1080px;
        }

        @media (max-width: 768px) {
            .image-contruct {
                width: 500px;
            }
        }
    </style>
</head>

<body>
    <img src="{{ asset('asset') }}/Perawatan.png" alt="" class="image-contruct">
</body>

</html>
