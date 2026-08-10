<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Carrito | Machin Barber</title>

    <style>
        *{ margin:0; padding:0; box-sizing:border-box; }

        body{ background:#15110c; color:#f3ead9; font-family:'Poppins',Arial,sans-serif; min-height:100vh; }

        .cart-page{ max-width:1100px; margin:auto; padding:60px 30px; }

        .cart-header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:40px; }

        .cart-title{ color:#d4af37; font-size:38px; }

        .back-link{ color:#d4af37; text-decoration:none; }

        .cart-container{ display:grid; grid-template-columns:1fr 320px; gap:35px; }

        .cart-products{ display:flex; flex-direction:column; gap:18px; }

        .cart-item{ display:grid; grid-template-columns:1fr auto auto; align-items:center; gap:25px; padding:22px; background:#1f1810; border:1px solid rgba(217,168,98,0.14); border-radius:18px; }

        .product-name{ color:white; font-size:18px; margin-bottom:8px; }

        .product-price{ color:#d4af37; font-size:15px; }

        .quantity-control{ display:flex; align-items:center; gap:12px; }

        .quantity-btn{ width:36px; height:36px; border:1px solid rgba(212,175,55,0.35); border-radius:8px; background:#15110c; color:#d4af37; cursor:pointer; font-size:18px; }

        .quantity{ min-width:25px; text-align:center; }

        .item-actions{ display:flex; flex-direction:column; align-items:flex-end; gap:12px; }

        .subtotal{ color:#d4af37; font-weight:600; }

        .remove-btn{ border:none; background:transparent; color:rgba(255,255,255,0.45); cursor:pointer; }

        .remove-btn:hover{ color:#d4af37; }

        .cart-summary{ height:max-content; padding:28px; background:#1f1810; border:1px solid rgba(212,175,55,0.18); border-radius:18px; }

        .cart-summary h2{ color:#d4af37; margin-bottom:25px; }

        .summary-row{ display:flex; justify-content:space-between; margin-bottom:20px; }

        .summary-total{ padding-top:20px; border-top:1px solid rgba(255,255,255,0.10); font-size:20px; }

        .summary-total strong{ color:#d4af37; }

        .checkout-btn{ width:100%; margin-top:25px; padding:15px; border:none; border-radius:10px; background:#d4af37; color:#000; font-weight:700; cursor:pointer; }

        .clear-btn{ width:100%; margin-top:12px; padding:13px; border:1px solid rgba(124,37,48,0.35); border-radius:10px; background:transparent; color:#e79a95; cursor:pointer; transition:0.2s; }

        .clear-btn:hover{ background:rgba(124,37,48,0.12); border-color:#7c2530; }

        .empty-cart{ padding:50px; text-align:center; background:#1f1810; border-radius:18px; color:rgba(255,255,255,0.60); }

        .loading{ text-align:center; padding:50px; color:rgba(255,255,255,0.60); }

        @media(max-width:800px){
            .cart-container{ grid-template-columns:1fr; }
            .cart-item{ grid-template-columns:1fr; }
            .item-actions{ align-items:flex-start; }
        }

        /* =====================================================
   CHECKOUT
===================================================== */

.checkout-modal{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.80);
    display:none;
    justify-content:center;
    align-items:center;
    padding:25px;
    z-index:1000;
}

.checkout-modal.active{
    display:flex;
}

.checkout-modal-content{
    position:relative;
    width:100%;
    max-width:600px;
    max-height:90vh;
    overflow-y:auto;
    padding:35px;
    background:#1f1810;
    border:1px solid rgba(212,175,55,0.25);
    border-radius:18px;
}

.checkout-close{
    position:absolute;
    top:18px;
    right:20px;
    border:none;
    background:transparent;
    color:white;
    font-size:20px;
    cursor:pointer;
}

.checkout-close:hover{
    color:#d4af37;
}

.checkout-title{
    color:#d4af37;
    font-size:28px;
    margin-bottom:8px;
}

.checkout-subtitle{
    color:rgba(255,255,255,0.60);
    margin-bottom:28px;
}

.delivery-options{
    display:flex;
    flex-direction:column;
    gap:15px;
}

.delivery-option{
    display:flex;
    gap:15px;
    align-items:flex-start;
    padding:18px;
    border:1px solid rgba(255,255,255,0.10);
    border-radius:12px;
    cursor:pointer;
}

.delivery-option:hover{
    border-color:rgba(212,175,55,0.50);
}

.delivery-option input{
    margin-top:4px;
    accent-color:#d4af37;
}

.delivery-option strong{
    display:block;
    color:white;
    margin-bottom:5px;
}

.delivery-option span{
    display:block;
    color:rgba(255,255,255,0.55);
    font-size:14px;
}

.directions-section{
    margin-top:28px;
}

.directions-section h3{
    color:#d4af37;
    margin-bottom:15px;
}

.direction-card{
    display:flex;
    align-items:flex-start;
    gap:12px;
    padding:16px;
    margin-bottom:10px;
    border:1px solid rgba(255,255,255,0.10);
    border-radius:10px;
    cursor:pointer;
}

.direction-card:hover{
    border-color:rgba(212,175,55,0.45);
}

.direction-card input{
    margin-top:4px;
    accent-color:#d4af37;
}

.direction-card strong{
    display:block;
    margin-bottom:5px;
}

.direction-card span{
    color:rgba(255,255,255,0.55);
    font-size:14px;
}

.directions-loading{
    color:rgba(255,255,255,0.55);
    padding:15px 0;
}

.no-directions{
    color:rgba(255,255,255,0.55);
    margin-bottom:15px;
}

.new-direction-btn{
    width:100%;
    padding:13px;
    margin-top:10px;
    background:transparent;
    border:1px solid rgba(212,175,55,0.35);
    border-radius:10px;
    color:#d4af37;
    cursor:pointer;
}

.direction-form{
    margin-top:20px;
    padding-top:20px;
    border-top:1px solid rgba(255,255,255,0.10);
}

.direction-form input,
.direction-form textarea{
    width:100%;
    margin-bottom:12px;
    padding:13px;
    border:1px solid rgba(255,255,255,0.10);
    border-radius:8px;
    background:#15110c;
    color:white;
    outline:none;
}

.direction-form input:focus,
.direction-form textarea:focus{
    border-color:#d4af37;
}

.direction-form textarea{
    min-height:90px;
    resize:vertical;
}

.save-direction-btn{
    width:100%;
    padding:13px;
    border:none;
    border-radius:9px;
    background:#d4af37;
    color:#000;
    font-weight:700;
    cursor:pointer;
}

.continue-payment-btn{
    width:100%;
    margin-top:30px;
    padding:15px;
    border:none;
    border-radius:10px;
    background:#d4af37;
    color:#000;
    font-weight:700;
    cursor:pointer;
}
    </style>
</head>

<body>

    <main class="cart-page">

        <div class="cart-header">
            <h1 class="cart-title">Tu carrito</h1>

            <a href="{{ route('home') }}" class="back-link">
                Seguir comprando
            </a>
        </div>


        <div class="cart-container">

            <div id="cartProducts" class="cart-products">

                <div class="loading">
                    Cargando carrito...
                </div>

            </div>


            <aside class="cart-summary">

                <h2>Resumen</h2>

                <div class="summary-row">
                    <span>Productos</span>
                    <span id="productCount">0</span>
                </div>

                <div class="summary-row summary-total">
                    <span>Total</span>
                    <strong>
                        $<span id="cartTotal">0.00</span>
                    </strong>
                </div>

                <button id="checkoutBtn" class="checkout-btn">
                    Proceder al pago
                </button>

                <button id="clearCartBtn" class="clear-btn">
                    Vaciar carrito
                </button>

            </aside>

        </div>

    </main>

<!-- =====================================================
     MODAL CHECKOUT
===================================================== -->

<div id="checkoutModal" class="checkout-modal">

    <div class="checkout-modal-content">

        <button
            type="button"
            id="closeCheckoutModal"
            class="checkout-close"
        >
            ✕
        </button>

        <h2 class="checkout-title">
            Finalizar compra
        </h2>

        <p class="checkout-subtitle">
            Selecciona cómo quieres recibir tu pedido.
        </p>


        <!-- MÉTODO DE ENTREGA -->

        <div class="delivery-options">

            <label class="delivery-option">

                <input
                    type="radio"
                    name="delivery_method"
                    value="pickup"
                    checked
                >

                <div>
                    <strong>
                        Recoger en barbería
                    </strong>

                    <span>
                        Recoge tu pedido directamente en Machin Barber.
                    </span>
                </div>

            </label>


            <label class="delivery-option">

                <input
                    type="radio"
                    name="delivery_method"
                    value="delivery"
                >

                <div>
                    <strong>
                        Envío a domicilio
                    </strong>

                    <span>
                        Recibe tu pedido en una dirección registrada.
                    </span>
                </div>

            </label>

        </div>


        <!-- DIRECCIONES -->

        <div
            id="directionsSection"
            class="directions-section"
            style="display:none;"
        >

            <h3>
                Dirección de entrega
            </h3>

            <div id="directionsContainer">

                <div class="directions-loading">
                    Cargando direcciones...
                </div>

            </div>


            <button
                type="button"
                id="showDirectionForm"
                class="new-direction-btn"
            >
                + Agregar nueva dirección
            </button>


            <!-- NUEVA DIRECCIÓN -->

            <div
                id="directionForm"
                class="direction-form"
                style="display:none;"
            >

                <h3>
                    Nueva dirección
                </h3>

                <input
                    type="text"
                    id="directionName"
                    placeholder="Nombre de la dirección (Casa, Trabajo...)"
                >

                <input
                    type="text"
                    id="directionState"
                    placeholder="Estado"
                >

                <input
                    type="text"
                    id="directionCity"
                    placeholder="Ciudad"
                >

                <input
                    type="text"
                    id="directionPostalCode"
                    placeholder="Código postal"
                >

                <input
                    type="text"
                    id="directionResidence"
                    placeholder="Calle y número"
                >

                <textarea
                    id="directionDescription"
                    placeholder="Colonia, referencias, número interior, etc."
                ></textarea>


                <button
                    type="button"
                    id="saveDirectionBtn"
                    class="save-direction-btn"
                >
                    Guardar dirección
                </button>

            </div>

        </div>


        <!-- CONTINUAR -->

        <button
            type="button"
            id="continuePaymentBtn"
            class="continue-payment-btn"
        >
            Continuar al pago
        </button>
        <div
    id="paypalSection"
    style="display:none; margin-top:20px;"
>
    <div id="paypal-button-container"></div>
</div>

    </div>

</div>
<script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency=MXN&intent=capture"></script>
<script>

    const cartProducts =
        document.getElementById("cartProducts");

    const cartTotal =
        document.getElementById("cartTotal");

    const productCount =
        document.getElementById("productCount");

    const clearCartBtn =
        document.getElementById("clearCartBtn");

    const checkoutBtn =
        document.getElementById("checkoutBtn");


    // =====================================================
    // CARGAR CARRITO
    // =====================================================

    async function loadCart(){

        try {

            const response =
                await fetch(
                    "/api/cart",
                    {
                        headers: {
                            "Accept": "application/json"
                        },

                        credentials: "same-origin"
                    }
                );


            const result =
                await response.json();


            if(!response.ok){

                if(response.status === 401){

                    window.location.href =
                        "{{ route('login') }}";

                    return;
                }

                throw new Error(
                    result.message ||
                    "No se pudo cargar el carrito."
                );
            }


            renderCart(
                result.data
            );


        }catch(error){

            console.error(
                "Error cargando carrito:",
                error
            );


            cartProducts.innerHTML = `
                <div class="empty-cart">
                    No se pudo cargar el carrito.
                </div>
            `;
        }
    }


    // =====================================================
    // MOSTRAR CARRITO
    // =====================================================

    function renderCart(cart){

        if(
            !cart ||
            !cart.producto_cart ||
            cart.producto_cart.length === 0
        ){

            cartProducts.innerHTML = `
                <div class="empty-cart">
                    Tu carrito está vacío.
                </div>
            `;

            cartTotal.textContent =
                "0.00";

            productCount.textContent =
                "0";

            return;
        }


        cartProducts.innerHTML =
            "";


        let totalProducts = 0;


        cart.producto_cart.forEach(
            item => {

                const product =
                    item.producto;


                if(!product){
                    return;
                }


                totalProducts +=
                    Number(item.quantity);


                const productItem =
                    document.createElement("div");


                productItem.classList.add(
                    "cart-item"
                );


                productItem.innerHTML = `

                    <div>
                        <div class="product-name">
                            ${product.name}
                        </div>

                        <div class="product-price">
                            $${Number(product.sell_price).toFixed(2)}
                        </div>
                    </div>


                    <div class="quantity-control">

                        <button
                            class="quantity-btn"
                            onclick="lessProduct(${product.productID})"
                        >
                            −
                        </button>

                        <span class="quantity">
                            ${item.quantity}
                        </span>

                        <button
                            class="quantity-btn"
                            onclick="moreProduct(${product.productID})"
                        >
                            +
                        </button>

                    </div>


                    <div class="item-actions">

                        <span class="subtotal">
                            $${Number(item.subtotal).toFixed(2)}
                        </span>

                        <button
                            class="remove-btn"
                            onclick="removeProduct(${product.productID})"
                        >
                            Eliminar
                        </button>

                    </div>
                `;


                cartProducts.appendChild(
                    productItem
                );
            }
        );


        cartTotal.textContent =
            Number(cart.total).toFixed(2);


        productCount.textContent =
            totalProducts;
    }


    // =====================================================
    // AUMENTAR PRODUCTO
    // =====================================================

    async function moreProduct(productID){

        await cartAction(
            `/api/cart/${productID}/more`,
            "POST"
        );
    }


    // =====================================================
    // DISMINUIR PRODUCTO
    // =====================================================

    async function lessProduct(productID){

        await cartAction(
            `/api/cart/${productID}/less`,
            "POST"
        );
    }


    // =====================================================
    // ELIMINAR PRODUCTO
    // =====================================================

    async function removeProduct(productID){

        await cartAction(
            `/api/cart/${productID}`,
            "DELETE"
        );
    }


    // =====================================================
    // ACCIONES DEL CARRITO
    // =====================================================

    async function cartAction(url, method){

        try {

            const response =
                await fetch(
                    url,
                    {
                        method: method,

                        headers: {
                            "Accept": "application/json"
                        },

                        credentials: "same-origin"
                    }
                );


            const result =
                await response.json();


            if(!response.ok){

                alert(
                    result.message ||
                    "No se pudo modificar el carrito."
                );

                return;
            }


            loadCart();


        }catch(error){

            console.error(
                "Error modificando carrito:",
                error
            );
        }
    }


    // =====================================================
    // VACIAR CARRITO
    // =====================================================

    clearCartBtn.addEventListener(
        "click",
        async () => {

            const confirmClear =
                confirm(
                    "¿Quieres vaciar tu carrito?"
                );


            if(!confirmClear){
                return;
            }


            await cartAction(
                "/api/cart",
                "DELETE"
            );
        }
    );

// =====================================================
// CHECKOUT
// =====================================================

const checkoutModal =
    document.getElementById("checkoutModal");

const closeCheckoutModal =
    document.getElementById("closeCheckoutModal");

const directionsSection =
    document.getElementById("directionsSection");

const directionsContainer =
    document.getElementById("directionsContainer");

const showDirectionForm =
    document.getElementById("showDirectionForm");

const directionForm =
    document.getElementById("directionForm");

const saveDirectionBtn =
    document.getElementById("saveDirectionBtn");

const continuePaymentBtn =
    document.getElementById("continuePaymentBtn");


// =====================================================
// ABRIR CHECKOUT
// =====================================================

checkoutBtn.addEventListener(
    "click",
    () => {

        if(Number(productCount.textContent) === 0){

            alert(
                "Tu carrito está vacío."
            );

            return;
        }

        checkoutModal.classList.add(
            "active"
        );
    }
);


// =====================================================
// CERRAR CHECKOUT
// =====================================================

closeCheckoutModal.addEventListener(
    "click",
    () => {

        checkoutModal.classList.remove(
            "active"
        );
    }
);


checkoutModal.addEventListener(
    "click",
    event => {

        if(event.target === checkoutModal){

            checkoutModal.classList.remove(
                "active"
            );
        }
    }
);


// =====================================================
// MÉTODO DE ENTREGA
// =====================================================

document
    .querySelectorAll(
        'input[name="delivery_method"]'
    )
    .forEach(input => {

        input.addEventListener(
            "change",
            () => {

                if(input.value === "delivery"){

                    directionsSection.style.display =
                        "block";

                    loadDirections();

                }else{

                    directionsSection.style.display =
                        "none";
                }
            }
        );
    });


// =====================================================
// CARGAR DIRECCIONES
// =====================================================

async function loadDirections(){

    directionsContainer.innerHTML = `
        <div class="directions-loading">
            Cargando direcciones...
        </div>
    `;

    try {

        const response =
            await fetch(
                "/directions",
                {
                    headers: {
                        "Accept": "application/json"
                    },

                    credentials: "same-origin"
                }
            );


        const result =
            await response.json();


        if(!response.ok){

            throw new Error(
                result.message ||
                "No se pudieron cargar las direcciones."
            );
        }


        renderDirections(
            result.data
        );


    }catch(error){

        console.error(
            "Error cargando direcciones:",
            error
        );

        directionsContainer.innerHTML = `
            <div class="no-directions">
                No se pudieron cargar las direcciones.
            </div>
        `;
    }
}


// =====================================================
// MOSTRAR DIRECCIONES
// =====================================================

function renderDirections(directions){

    directionsContainer.innerHTML =
        "";


    if(
        !directions ||
        directions.length === 0
    ){

        directionsContainer.innerHTML = `
            <div class="no-directions">
                Todavía no tienes direcciones guardadas.
            </div>
        `;

        return;
    }


    directions.forEach(
        direction => {

            const label =
                document.createElement(
                    "label"
                );


            label.classList.add(
                "direction-card"
            );


            label.innerHTML = `

                <input
                    type="radio"
                    name="directionID"
                    value="${direction.directionID}"
                >

                <div>

                    <strong>
                        ${direction.name}
                    </strong>

                    <span>
                        ${direction.residence},
                        ${direction.city},
                        ${direction.state},
                        C.P. ${direction.postal_code}
                    </span>

                </div>
            `;


            directionsContainer.appendChild(
                label
            );
        }
    );
}


// =====================================================
// MOSTRAR FORMULARIO DIRECCIÓN
// =====================================================

showDirectionForm.addEventListener(
    "click",
    () => {

        if(
            directionForm.style.display === "none" ||
            directionForm.style.display === ""
        ){

            directionForm.style.display =
                "block";

            showDirectionForm.textContent =
                "Cancelar";

        }else{

            directionForm.style.display =
                "none";

            showDirectionForm.textContent =
                "+ Agregar nueva dirección";
        }
    }
);


// =====================================================
// GUARDAR DIRECCIÓN
// =====================================================

saveDirectionBtn.addEventListener(
    "click",
    async () => {

        const data = {

            name:
                document
                    .getElementById(
                        "directionName"
                    )
                    .value
                    .trim(),

            state:
                document
                    .getElementById(
                        "directionState"
                    )
                    .value
                    .trim(),

            city:
                document
                    .getElementById(
                        "directionCity"
                    )
                    .value
                    .trim(),

            postal_code:
                document
                    .getElementById(
                        "directionPostalCode"
                    )
                    .value
                    .trim(),

            residence:
                document
                    .getElementById(
                        "directionResidence"
                    )
                    .value
                    .trim(),

            description:
                document
                    .getElementById(
                        "directionDescription"
                    )
                    .value
                    .trim()
        };


        if(
            !data.name ||
            !data.state ||
            !data.city ||
            !data.postal_code ||
            !data.residence ||
            !data.description
        ){

            alert(
                "Completa todos los campos de la dirección."
            );

            return;
        }


        try {

            saveDirectionBtn.disabled =
                true;

            saveDirectionBtn.textContent =
                "Guardando...";


            const response =
                await fetch(
                    "/directions",
                    {
                        method: "POST",

                        headers: {
                            "Content-Type":
                                "application/json",

                            "Accept":
                                "application/json"
                        },

                        credentials:
                            "same-origin",

                        body:
                            JSON.stringify(
                                data
                            )
                    }
                );


            const result =
                await response.json();


            if(!response.ok){

                alert(
                    result.message ||
                    "No se pudo guardar la dirección."
                );

                return;
            }


            directionForm.style.display =
                "none";

            showDirectionForm.textContent =
                "+ Agregar nueva dirección";


            await loadDirections();


        }catch(error){

            console.error(
                "Error guardando dirección:",
                error
            );

            alert(
                "Ocurrió un error al guardar la dirección."
            );


        }finally{

            saveDirectionBtn.disabled =
                false;

            saveDirectionBtn.textContent =
                "Guardar dirección";
        }
    }
);


// =====================================================
// PAYPAL
// =====================================================

let checkoutDeliveryMethod = null;
let checkoutDirectionID = null;
let paypalButtonsRendered = false;


// =====================================================
// CONTINUAR AL PAGO
// =====================================================

continuePaymentBtn.addEventListener(
    "click",
    () => {

        const selectedMethod =
            document.querySelector(
                'input[name="delivery_method"]:checked'
            );


        if(!selectedMethod){

            alert(
                "Selecciona un método de entrega."
            );

            return;
        }


        checkoutDeliveryMethod =
            selectedMethod.value;


        checkoutDirectionID =
            null;


        // =====================================================
        // VALIDAR DIRECCIÓN
        // =====================================================

        if(
            checkoutDeliveryMethod ===
            "delivery"
        ){

            const selectedDirection =
                document.querySelector(
                    'input[name="directionID"]:checked'
                );


            if(!selectedDirection){

                alert(
                    "Selecciona una dirección de entrega."
                );

                return;
            }


            checkoutDirectionID =
                selectedDirection.value;
        }


        // =====================================================
        // MOSTRAR PAYPAL
        // =====================================================

        document
            .getElementById(
                "paypalSection"
            )
            .style
            .display =
                "block";


        continuePaymentBtn.style.display =
            "none";


        renderPayPalButtons();
    }
);


// =====================================================
// RENDERIZAR BOTONES PAYPAL
// =====================================================

function renderPayPalButtons(){

    if(paypalButtonsRendered){
        return;
    }


    paypalButtonsRendered =
        true;


    paypal.Buttons({

        style: {
            layout: "vertical",
            shape: "rect",
            label: "paypal"
        },


        // =====================================================
        // CREAR ORDEN
        // =====================================================

        createOrder: async function(){

            try {

                const response =
                    await fetch(
                        "/paypal/create-order",
                        {
                            method: "POST",

                            headers: {
                                "Content-Type":
                                    "application/json",

                                "Accept":
                                    "application/json"
                            },

                            credentials:
                                "same-origin",

                            body:
                                JSON.stringify({

                                    delivery_method:
                                        checkoutDeliveryMethod,

                                    directionID:
                                        checkoutDirectionID
                                })
                        }
                    );


                const result =
                    await response.json();


                if(!response.ok){

                    throw new Error(
                        result.message ||
                        "No se pudo crear la orden."
                    );
                }


                console.log(
                    "Orden PayPal creada:",
                    result.orderID
                );


                return result.orderID;


            }catch(error){

                console.error(
                    "Error creando orden PayPal:",
                    error
                );


                alert(
                    error.message
                );


                throw error;
            }
        },


        // =====================================================
        // PAGO APROBADO
        // =====================================================

        onApprove: async function(data){

            try {

                const response =
                    await fetch(
                        "/paypal/capture-order",
                        {
                            method: "POST",

                            headers: {
                                "Content-Type":
                                    "application/json",

                                "Accept":
                                    "application/json"
                            },

                            credentials:
                                "same-origin",

                            body:
                                JSON.stringify({
                                    orderID:
                                        data.orderID
                                })
                        }
                    );


                const result =
                    await response.json();


                if(!response.ok){

                    throw new Error(
                        result.message ||
                        "No se pudo confirmar el pago."
                    );
                }


                console.log(
                    "Pago PayPal:",
                    result
                );


                alert(
                    "Pago realizado correctamente."
                );


                checkoutModal
                    .classList
                    .remove(
                        "active"
                    );

// Volver a consultar el carrito
await loadCart();

            }catch(error){

                console.error(
                    "Error capturando PayPal:",
                    error
                );


                alert(
                    error.message
                );
            }
        },


        // =====================================================
        // PAGO CANCELADO
        // =====================================================

        onCancel: function(){

            alert(
                "El pago fue cancelado."
            );
        },


        // =====================================================
        // ERROR PAYPAL
        // =====================================================

        onError: function(error){

            console.error(
                "Error PayPal:",
                error
            );


            alert(
                "Ocurrió un error con PayPal."
            );
        }


    }).render(
        "#paypal-button-container"
    );
}
    // =====================================================
    // INICIALIZACIÓN
    // =====================================================

    loadCart();

</script>

</body>
</html>