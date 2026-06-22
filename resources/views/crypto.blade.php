<!DOCTYPE html>
<html>
<head>
    <title>AES-256-GCM Tool</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow">

        <div class="card-header bg-primary text-white">
            <h3>AES-256-GCM Encryption & Decryption</h3>
        </div>

        <div class="card-body">

            <ul class="nav nav-tabs" id="cryptoTabs">

                <li class="nav-item">
                    <button class="nav-link active"
                            data-bs-toggle="tab"
                            data-bs-target="#encrypt">
                        Encrypt
                    </button>
                </li>

                <li class="nav-item">
                    <button class="nav-link"
                            data-bs-toggle="tab"
                            data-bs-target="#decrypt">
                        Decrypt
                    </button>
                </li>

            </ul>

            <div class="tab-content mt-4">

                <!-- Encrypt Tab -->
                <div class="tab-pane fade show active"
                     id="encrypt">

                    <form action="/encrypt"
                          method="POST">

                        @csrf

                        <div class="mb-3">
                            <label class="form-label">
                                Plain Text
                            </label>

                            <textarea
                                class="form-control"
                                rows="5"
                                name="plain_text"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                AES Key (32 Bytes)
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                name="key">
                        </div>

                        <button class="btn btn-success">
                            Encrypt
                        </button>

                    </form>

                    @if(session('encrypted'))

                        <hr>

                        <h5>Encrypted Result</h5>

                        <textarea
                            class="form-control"
                            rows="5"
                            readonly>{{ session('encrypted') }}</textarea>

                    @endif

                </div>

                <!-- Decrypt Tab -->
                <div class="tab-pane fade"
                     id="decrypt">

                    <form action="/decrypt"
                          method="POST">

                        @csrf

                        <div class="mb-3">
                            <label class="form-label">
                                Encrypted Payload
                            </label>

                            <textarea
                                class="form-control"
                                rows="5"
                                name="encrypted_text"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                AES Key
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                name="key">
                        </div>

                        <button class="btn btn-danger">
                            Decrypt
                        </button>

                    </form>

                    @if(session('decrypted'))

                        <hr>

                        <h5>Decrypted Result</h5>

                        <textarea
                            class="form-control"
                            rows="5"
                            readonly>{{ session('decrypted') }}</textarea>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>