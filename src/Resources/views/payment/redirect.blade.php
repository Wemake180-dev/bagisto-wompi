<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pago Seguro - Wompi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            max-width: 480px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
        }
        
        h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .content {
            padding: 30px;
        }
        
        .summary {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .summary-item:last-child {
            border-bottom: none;
            padding-top: 15px;
            margin-top: 5px;
            border-top: 2px solid #dee2e6;
        }
        
        .summary-label {
            font-size: 14px;
            color: #6c757d;
            font-weight: 500;
        }
        
        .summary-value {
            font-size: 14px;
            color: #212529;
            font-weight: 600;
        }
        
        .summary-total {
            font-size: 18px !important;
            color: #667eea !important;
        }
        
        .payment-methods {
            margin: 25px 0;
            text-align: center;
        }
        
        .payment-methods-title {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .payment-icons {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .payment-icon {
            width: 50px;
            height: 32px;
            background: #f8f9fa;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #6c757d;
            border: 1px solid #e9ecef;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 40px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 30px rgba(102, 126, 234, 0.5);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .btn-primary svg {
            width: 20px;
            height: 20px;
        }
        
        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 20px 0;
            font-size: 13px;
            color: #6c757d;
        }
        
        .security-badge svg {
            width: 16px;
            height: 16px;
            color: #28a745;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
        }
        
        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid #f3f4f6;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .loading-text {
            font-size: 14px;
            color: #6c757d;
        }
        
        .error {
            background: #fee;
            border: 1px solid #fcc;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .error-icon {
            width: 48px;
            height: 48px;
            background: #dc3545;
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #6c757d;
            text-decoration: none;
            font-size: 14px;
            margin-top: 20px;
            transition: color 0.3s ease;
        }
        
        .back-link:hover {
            color: #667eea;
        }
        
        .back-link svg {
            width: 16px;
            height: 16px;
        }
        
        .hidden {
            display: none !important;
        }
        
        @media (max-width: 480px) {
            .container {
                border-radius: 0;
                min-height: 100vh;
            }
            
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                💳
            </div>
            <h1>Pago Seguro</h1>
            <p>Procesamiento protegido por Wompi</p>
        </div>
        
        <div class="content">
            <div class="summary">
                <div class="summary-item">
                    <span class="summary-label">Referencia</span>
                    <span class="summary-value">#{{ substr($reference, -8) }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Descripción</span>
                    <span class="summary-value">Compra en tienda</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label summary-total">Total a pagar</span>
                    <span class="summary-value summary-total">{{ core()->formatPrice($amount / 100, $currency) }}</span>
                </div>
            </div>
            
            <div class="payment-methods">
                <div class="payment-methods-title">Métodos de pago aceptados</div>
                <div class="payment-icons">
                    <div class="payment-icon">VISA</div>
                    <div class="payment-icon">Master Card</div>
                    <div class="payment-icon">CLAVE</div>
                </div>
            </div>
            
            <div id="loading" class="loading hidden">
                <div class="spinner"></div>
                <div class="loading-text">Preparando formulario de pago...</div>
            </div>
            
            <div id="wompi-widget">
                <button id="wompi-payment-button" class="btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Proceder al Pago Seguro
                </button>
            </div>
            
            <div id="error-container" class="error hidden">
                <div class="error-icon">⚠</div>
                <div class="error-message" id="error-message"></div>
                <button onclick="location.reload()" class="btn-primary">Reintentar</button>
            </div>
            
            <div class="security-badge">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                Conexión segura y encriptada
            </div>
            
            <center>
                <a href="{{ route('shop.checkout.cart.index') }}" class="back-link">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Volver al carrito
                </a>
            </center>
        </div>
    </div>

    <script src="https://checkout.wompi.pa/widget.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            try {
                if (typeof WidgetCheckout === 'undefined') {
                    showError('El servicio de pago está temporalmente no disponible. Por favor, intente más tarde.');
                    return;
                }

                var checkout = new WidgetCheckout({
                    currency: '{{ $currency }}',
                    amountInCents: {{ $amount }},
                    reference: '{{ $reference }}',
                    publicKey: '{{ $publicKey }}',
                    signature: {
                        integrity: '{{ $signature }}'
                    },
                    redirectUrl: '{{ route("wompi.success") }}',
                    @if($customerEmail)
                    customerData: {
                        email: '{{ $customerEmail }}',
                        fullName: '{{ $customerName ?? "" }}'
                    },
                    @endif
                });

                var button = document.getElementById('wompi-payment-button');
                button.addEventListener('click', function() {
                    // Disable button and show loading state
                    button.disabled = true;
                    button.innerHTML = '<div class="spinner" style="width: 20px; height: 20px; border-width: 2px; margin: 0;"></div> Procesando...';
                    
                    checkout.open(function (result) {
                        // Re-enable button
                        button.disabled = false;
                        button.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg> Proceder al Pago Seguro';
                        
                        if (result.transaction) {
                            var transaction = result.transaction;
                            console.log('Transaction:', transaction);
                            
                            if (transaction.status === 'APPROVED') {
                                showSuccess('¡Pago aprobado! Redirigiendo...');
                                setTimeout(function() {
                                    window.location.href = '{{ route("wompi.success") }}?id=' + transaction.id;
                                }, 1500);
                            } else if (transaction.status === 'DECLINED') {
                                showError('Pago declinado. ' + (transaction.status_message || 'Por favor, verifique su información e intente nuevamente.'));
                            } else if (transaction.status === 'ERROR') {
                                showError('Error en el procesamiento. ' + (transaction.status_message || 'Por favor, intente más tarde.'));
                            } else {
                                // PENDING or other status
                                showSuccess('Procesando pago...');
                                setTimeout(function() {
                                    window.location.href = '{{ route("wompi.success") }}?id=' + transaction.id;
                                }, 2000);
                            }
                        } else if (result.error) {
                            showError(result.error.message || 'Error al procesar el pago. Por favor, intente nuevamente.');
                        } else {
                            // User closed the widget without completing payment
                            console.log('Payment cancelled by user');
                        }
                    });
                });

            } catch (error) {
                console.error('Wompi Widget Error:', error);
                showError('Error al inicializar el sistema de pago. Por favor, recargue la página.');
            }
        }, 1000); // Wait 1 second for widget to load
    });

    function showError(message) {
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('wompi-widget').classList.add('hidden');
        document.getElementById('error-message').textContent = message;
        document.getElementById('error-container').classList.remove('hidden');
    }
    
    function showSuccess(message) {
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('wompi-widget').classList.add('hidden');
        document.getElementById('error-container').classList.add('hidden');
        
        // Create success element
        var successHtml = '<div style="text-align: center; padding: 20px;">' +
            '<div style="width: 60px; height: 60px; background: #28a745; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; color: white; font-size: 30px;">✓</div>' +
            '<h3 style="color: #28a745; margin-bottom: 10px;">' + message + '</h3>' +
            '<div class="spinner" style="margin: 20px auto;"></div>' +
            '</div>';
        
        var tempDiv = document.createElement('div');
        tempDiv.innerHTML = successHtml;
        document.querySelector('.content').insertBefore(tempDiv.firstChild, document.querySelector('.security-badge'));
    }
    </script>
</body>
</html>