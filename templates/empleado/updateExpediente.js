const firebaseConfig = {
    apiKey: "AIzaSyBTs0UeS2aGQvuJUWK74Ndz8Md8AFEkr_0",
    authDomain: "nominasolidarista-storage.firebaseapp.com",
    projectId: "nominasolidarista-storage",
    storageBucket: "nominasolidarista-storage.appspot.com",
    messagingSenderId: "1034029997948",
    appId: "1:1034029997948:web:38d08b2506c73bd37d0ef9",
    measurementId: "G-54VYJQS0ZZ"
    };
    
    // Inicializa Firebase
  const app = firebase.initializeApp(firebaseConfig);
  const storage = firebase.storage();  // Initialize Firebase Storage
  
  function irAlInicio(){
    console.log("Usuario ha iniciado sesión:");
  
  }
  
  
  function loginpdf() {
    var emailtv = "nominaproyecto8@gmail.com";
    var passwordtv = "Nomina@#2";
  
    if (!validateEmail(emailtv)) {
        //Validar si el email es correcto
        alert('Por favor, ingresa un correo electrónico válido.');
        return;
    } else if (passwordtv.trim() === '') {
        //validar que el password no este vacio 
        alert('Por favor, ingresa una contraseña.');
        return;
    } else  if(passwordtv.length < 7){
        //verificar que el password tenga como minimo 7 caracteres
        alert('La contraseña por lo menos debe de tener 7 caracteres');
        return;
    } else{
      verifyLoginPDF(emailtv, passwordtv)
    }
  }
  
  function verifyLoginPDF(emailtv, passwordtv){
    // Inicia sesión con Firebase Auth
    firebase.auth().signInWithEmailAndPassword(emailtv, passwordtv)
    .then((userCredential) => {
        // El usuario ha iniciado sesión con éxito
        var user = userCredential.user;
   
        seleccionar_pdf()
   
        console.log("Usuario ha iniciado sesión:", user);
        // Aquí puedes redirigir a la página principal o realizar otras acciones
    })
    .catch((error) => {
        // Se produjo un error al iniciar sesión, puedes mostrar un mensaje de error al usuario
        var errorCode = error.code;
        var errorMessage = error.message;
        console.error("Error al iniciar sesión:", errorCode, errorMessage);
    });
   
   }
  
   function seleccionar_pdf() {
     const input = document.createElement('input');
     input.type = 'file';
     input.accept = 'application/pdf'; // Allow only PDF files
   
     input.onchange = async (event) => {
       const file = event.target.files[0];
       if (file) {
         subirPDF(file);
       }
     };
   
     input.click(); // Open the file input dialog
   }
   
   function subirPDF(file) {
     const storageRef = storage.ref('pdfs/' + file.name);
   
     // Upload file
     const task = storageRef.put(file);
   
     // Monitor the upload task
     task.on(
       'state_changed',
       (snapshot) => {
         // Progress
         const progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
         console.log('Upload is ' + progress + '% done');
       },
       (error) => {
         // Error
         console.error('Error uploading file: ', error);
       },
       () => {
         // Success, get download URL
         task.snapshot.ref.getDownloadURL().then((downloadURL) => {
           // Save download URL or display it wherever you need
           console.log('Download URL:', downloadURL);
           document.getElementById('archivo_pdf').value = downloadURL;
         });
       }
     );
   }
  
   function validateEmail(emailtv) {
    // Validación básica de correo electrónico
    // Puedes implementar una validación más detallada si lo deseas
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailPattern.test(emailtv);
  }