<!DOCTYPE html>
<html>
<head>
    <title>Google Login Demo</title>
    <meta name="google-signin-client_id" content="YOUR_GOOGLE_CLIENT_ID">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script>
        function handleCredentialResponse(response) {
            console.log("Encoded JWT ID token: " + response.credential);
            
            // You can post the token directly to your Laravel backend
            fetch("/api/google-auth", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    id_token: response.credential
                })
            })
            .then(res => res.json())
            .then(data => {
                console.log(data);
                alert("Logged in! Access token in console.");
            })
            .catch(err => {
                console.error(err);
                alert("Login failed.");
            });
        }
    </script>
</head>
<body>
    <h1>Login with Google</h1>
    <div id="g_id_onload"
         data-client_id="YOUR_GOOGLE_CLIENT_ID"
         data-callback="handleCredentialResponse">
    </div>
    <div class="g_id_signin"
         data-type="standard"
         data-size="large"
         data-theme="outline"
         data-text="sign_in_with"
         data-shape="rectangular"
         data-logo_alignment="left">
    </div>
</body>
</html>
