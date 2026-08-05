<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Crypto Tool</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="card shadow">

        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">
                AES-256 Encryption Tool
            </h3>
        </div>

        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <ul class="nav nav-tabs" id="cryptoTabs" role="tablist">

                <li class="nav-item">
                    <button
                        class="nav-link active"
                        id="encrypt-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#encrypt"
                        type="button">
                        AES Encrypt
                    </button>
                </li>

                <li class="nav-item">
                    <button
                        class="nav-link"
                        id="decrypt-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#decrypt"
                        type="button">
                        AES Decrypt
                    </button>
                </li>

                <li class="nav-item">
                    <button
                        class="nav-link"
                        id="encrypt-rsa-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#encrypt-rsa"
                        type="button">
                        AES + RSA Encrypt
                    </button>
                </li>

                <li class="nav-item">
                    <button
                        class="nav-link"
                        id="decrypt-rsa-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#decrypt-rsa"
                        type="button">
                        AES + RSA Decrypt
                    </button>
                </li>

            </ul>

            <div class="tab-content mt-4">

                {{-- AES Encrypt --}}

                <div class="tab-pane fade show active"
                     id="encrypt">

                    <form method="POST"
                          action="/encrypt">

                        @csrf

                        <div class="mb-3">

                            <label class="form-label">
                                Plain Text
                            </label>

                            <textarea
                                class="form-control"
                                rows="6"
                                name="plain_text">{{ old('plain_text') }}</textarea>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                AES Key (32 Bytes)
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                name="key"
                                value="{{ old('key') }}">

                        </div>

                        <button class="btn btn-success">
                            Encrypt
                        </button>

                    </form>

                    @if(session('encrypted'))

                        <hr>

                        <h5>
                            Encrypted Result
                        </h5>

                        <textarea
                            class="form-control"
                            rows="8"
                            readonly>{{ session('encrypted') }}</textarea>

                    @endif

                </div>

                {{-- AES Decrypt --}}

                <div class="tab-pane fade"
                     id="decrypt">

                    <form method="POST"
                          action="/decrypt">

                        @csrf

                        <div class="mb-3">

                            <label class="form-label">
                                Encrypted Payload
                            </label>

                            <textarea
                                class="form-control"
                                rows="6"
                                name="encrypted_text">{{ old('encrypted_text') }}</textarea>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                AES Key (32 Bytes)
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                name="key"
                                value="{{ old('key') }}">

                        </div>

                        <button class="btn btn-danger">
                            Decrypt
                        </button>

                    </form>

                    @if(session('decrypted'))

                        <hr>

                        <h5>
                            Decrypted Result
                        </h5>

                        <textarea
                            class="form-control"
                            rows="8"
                            readonly>{{ session('decrypted') }}</textarea>

                    @endif

                </div>
                {{-- AES + RSA Encrypt --}}

                <div class="tab-pane fade"
                     id="encrypt-rsa">

                    <form method="POST"
                          action="/encrypt-rsa">

                        @csrf

                        <div class="mb-3">

                            <label class="form-label">
                                Plain Text
                            </label>

                            <textarea
                                class="form-control"
                                rows="6"
                                name="plain_text">{{ old('plain_text') }}</textarea>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Private Key (PEM)
                            </label>

                            <textarea
                                class="form-control"
                                rows="10"
                                name="private_key">{{ old('private_key') }}</textarea>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Public Key / Certificate (PEM)
                            </label>

                            <textarea
                                class="form-control"
                                rows="10"
                                name="public_key">{{ old('public_key') }}</textarea>

                        </div>

                        <button class="btn btn-success">
                            Encrypt (AES + RSA)
                        </button>

                    </form>

                    @if(session('encrypted_rsa'))

                        <hr>

                        <h5>Encrypted Result</h5>

                        <textarea
                            class="form-control"
                            rows="10"
                            readonly>{{ session('encrypted_rsa') }}</textarea>

                    @endif

                </div>

                {{-- AES + RSA Decrypt --}}

                <div class="tab-pane fade"
                     id="decrypt-rsa">

                    <form method="POST"
                          action="/decrypt-rsa">

                        @csrf

                        <div class="mb-3">

                            <label class="form-label">
                                Encrypted Payload
                            </label>

                            <textarea
                                class="form-control"
                                rows="8"
                                name="encrypted_text">{{ old('encrypted_text') }}</textarea>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Private Key (PEM)
                            </label>

                            <textarea
                                class="form-control"
                                rows="10"
                                name="private_key">{{ old('private_key') }}</textarea>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Public Key / Certificate (PEM)
                            </label>

                            <textarea
                                class="form-control"
                                rows="10"
                                name="public_key">{{ old('public_key') }}</textarea>

                        </div>

                        <button class="btn btn-danger">
                            Decrypt (AES + RSA)
                        </button>

                    </form>

                    @if(session('decrypted_rsa'))

                        <hr>

                        <h5>Decrypted Result</h5>

                        <textarea
                            class="form-control"
                            rows="10"
                            readonly>{{ session('decrypted_rsa') }}</textarea>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    let activeTab = "{{ session('active_tab', 'encrypt') }}";

    const trigger = document.querySelector(
        '[data-bs-target="#' + activeTab + '"]'
    );

    if (trigger) {
        new bootstrap.Tab(trigger).show();
    }

});
</script>

</body>

</html>