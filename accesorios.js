// Verificar sesión y actualizar UI
const verificarSesion = async () => {
	try {
		const response = await fetch('verificar-sesion.php');
		const data = await response.json();
		
		const userContainer = document.getElementById('user-container');
		
		if (userContainer) {
			if (data.autenticado) {
				// Usuario autenticado - reemplazar el contenido del contenedor
				userContainer.innerHTML = `
					<span style="color: aliceblue; margin-right: 15px; font-size: 14px;">${data.usuario_nombre}</span>
					<a href="cerrar-sesion.php" style="color: aliceblue; cursor: pointer; font-size: 14px; text-decoration: none;">Cerrar Sesión</a>
				`;
			} else {
				// Usuario no autenticado - mantener el icono de login
				userContainer.innerHTML = `
					<a href="login.html">
						<img src="imagenes/usuario.png" id="icono_user" alt="User Icon" style="width: 40px; height: 40px; cursor: pointer;">
					</a>
				`;
			}
		}
	} catch (error) {
		console.error('Error al verificar sesión:', error);
	}
};

// Ejecutar verificación cuando se carga el DOM
document.addEventListener('DOMContentLoaded', verificarSesion);

const detectProductType = () => {
	const currentPage = window.location.href;
	
	if (currentPage.includes('principal-relojes-damas')) {
		return 'Reloj de Mujer';
	} else if (currentPage.includes('principal-relojes-caballeros')) {
		return 'Reloj de Caballero';
	} else if (currentPage.includes('accesorios')) {
		return 'Accesorio';
	} else {
		// Para index.html o principal-populares, retornar null para que use el atributo HTML
		return null;
	}
};

const btnsFavorite = document.querySelectorAll('.favorite');
const btnCart = document.querySelectorAll('.cart');
const products = document.querySelectorAll('.card-product');
const counterFavorites = document.querySelector('.counter-favorite');
const counterCart = document.querySelector('.counter-cart');

const containerListFavorites = document.querySelector(
	'.container-list-favorites'
);
const listFavorites = document.querySelector('.list-favorites');

const containerListCart = document.querySelector(
	'.container-list-cart'
);
const listCart = document.querySelector('.list-cart');

let favorites = [];
let cart = [];

const updateFavoritesInLocalStorage = () => {
	localStorage.setItem('favorites', JSON.stringify(favorites));
};

const updateCartInLocalStorage = () => {
	localStorage.setItem('cart', JSON.stringify(cart));
};

const loadFavoritesFromLocalStorage = () => {
	const storedFavorites = localStorage.getItem('favorites');

	if (storedFavorites) {
		favorites = JSON.parse(storedFavorites);
		showHTML();
	}
};

const loadCartFromLocalStorage = () => {
	const storedCart = localStorage.getItem('cart');

	if (storedCart) {
		cart = JSON.parse(storedCart);
		// Asegurar que cada producto tiene un tipo
		cart.forEach(item => {
			if (!item.type) {
				const typeDetected = detectProductType();
				item.type = typeDetected || 'Reloj';
			}
		});
		updateCartInLocalStorage();
		updateCartCounter();
	}
};

const toggleFavorite = product => {
	const index = favorites.findIndex(
		element => element.id === product.id
	);

	if (index > -1) {
		favorites.splice(index, 1);
		updateFavoritesInLocalStorage();
	} else {
		favorites.push(product);
		updateFavoritesInLocalStorage();
	}
};

const addToCart = product => {
	const existingProduct = cart.findIndex(
		element => element.id === product.id
	);

	if (existingProduct > -1) {
		cart[existingProduct].quantity = (cart[existingProduct].quantity || 0) + 1;
	} else {
		product.quantity = 1;
		cart.push(product);
	}

	updateCartInLocalStorage();
	updateCartCounter();
	updateCartMenu();
	alert(`${product.title} agregado al carrito`);
};

const updateCartCounter = () => {
	const totalItems = cart.reduce((sum, product) => sum + product.quantity, 0);
	counterCart.textContent = totalItems;
};

const updateFavoriteMenu = () => {
	listFavorites.innerHTML = '';

	favorites.forEach(fav => {
		// Crear un nuevo elemento 'div' para el producto favorito
		const favoriteCard = document.createElement('div');
		favoriteCard.classList.add('card-favorite');

		// Crear y añadir el título del producto
		const titleElement = document.createElement('p');
		titleElement.classList.add('title');
		titleElement.textContent = fav.title;
		favoriteCard.appendChild(titleElement);

		// Crear y añadir el tipo del producto (cambiar Mujer por Dama)
		let typeText = fav.type || 'Producto';
		typeText = typeText.replace('Reloj de Mujer', 'Reloj de Dama');
		const typeElement = document.createElement('p');
		typeElement.classList.add('type-fav');
		typeElement.textContent = typeText;
		favoriteCard.appendChild(typeElement);

		// Crear contenedor para precio y botón
		const footerContainer = document.createElement('div');
		footerContainer.style.display = 'flex';
		footerContainer.style.justifyContent = 'space-between';
		footerContainer.style.alignItems = 'center';

		// Crear y añadir el precio del producto
		const priceElement = document.createElement('p');
		priceElement.style.margin = '0';
		priceElement.textContent = fav.price;
		footerContainer.appendChild(priceElement);

		// Crear botón de eliminar
		const deleteBtn = document.createElement('button');
		deleteBtn.classList.add('delete-fav-btn');
		deleteBtn.textContent = '✕';
		deleteBtn.addEventListener('click', () => {
			favorites = favorites.filter(p => p.id !== fav.id);
			updateFavoritesInLocalStorage();
			updateFavoriteMenu();
			showHTML();
		});
		footerContainer.appendChild(deleteBtn);

		favoriteCard.appendChild(footerContainer);

		// Añadir el producto favorito a la lista
		listFavorites.appendChild(favoriteCard);
	});
};

const updateCartMenu = () => {
	if (!listCart) return;
	
	listCart.innerHTML = '';

	if (cart.length === 0) {
		const emptyMessage = document.createElement('p');
		emptyMessage.classList.add('empty-message');
		emptyMessage.textContent = 'El carrito está vacío';
		listCart.appendChild(emptyMessage);
		return;
	}

	let totalPrice = 0;

	cart.forEach(item => {
		const cartCard = document.createElement('div');
		cartCard.classList.add('card-cart');

		const titleElement = document.createElement('p');
		titleElement.classList.add('title');
		titleElement.textContent = item.title;
		cartCard.appendChild(titleElement);

		const typeElement = document.createElement('p');
		typeElement.classList.add('type');
		const typeDetected = detectProductType();
		typeElement.textContent = item.type || typeDetected || 'Reloj';
		cartCard.appendChild(typeElement);

		const priceElement = document.createElement('p');
		priceElement.classList.add('price');
		priceElement.textContent = item.price;
		cartCard.appendChild(priceElement);

		const quantityContainer = document.createElement('div');
		quantityContainer.classList.add('quantity-container');

		const minusBtn = document.createElement('button');
		minusBtn.textContent = '-';
		minusBtn.addEventListener('click', () => {
			item.quantity -= 1;
			if (item.quantity <= 0) {
				cart = cart.filter(p => p.id !== item.id);
			}
			updateCartInLocalStorage();
			updateCartCounter();
			updateCartMenu();
		});

		const quantitySpan = document.createElement('span');
		quantitySpan.textContent = item.quantity;
		quantitySpan.style.margin = '0 10px';

		const plusBtn = document.createElement('button');
		plusBtn.textContent = '+';
		plusBtn.addEventListener('click', () => {
			item.quantity += 1;
			updateCartInLocalStorage();
			updateCartCounter();
			updateCartMenu();
		});

		quantityContainer.appendChild(minusBtn);
		quantityContainer.appendChild(quantitySpan);
		quantityContainer.appendChild(plusBtn);
		cartCard.appendChild(quantityContainer);

		const deleteBtn = document.createElement('button');
		deleteBtn.classList.add('delete-btn');
		deleteBtn.textContent = '🗑️';
		deleteBtn.addEventListener('click', () => {
			cart = cart.filter(p => p.id !== item.id);
			updateCartInLocalStorage();
			updateCartCounter();
			updateCartMenu();
		});
		cartCard.appendChild(deleteBtn);

		const priceNumber = parseFloat(item.price.replace(/[^\d.-]/g, ''));
		totalPrice += priceNumber * item.quantity;

		listCart.appendChild(cartCard);
	});

	const totalElement = document.createElement('div');
	totalElement.classList.add('cart-total');
	totalElement.innerHTML = `<strong>Total: $${totalPrice.toFixed(2)}</strong>`;
	listCart.appendChild(totalElement);

	// Agregar botón de compra
	const purchaseBtn = document.createElement('button');
	purchaseBtn.classList.add('purchase-btn');
	purchaseBtn.textContent = 'Realizar Compra';
	purchaseBtn.addEventListener('click', async () => {
		if (cart.length > 0) {
			// Guardar compra en BD
			const compraData = {
				items: cart,
				total: totalPrice.toFixed(2),
				fecha: new Date().toISOString().split('T')[0]
			};

			try {
				const response = await fetch('guardar-compra.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					},
					body: JSON.stringify(compraData)
				});

				const result = await response.json();
				
				if (result.require_login) {
					alert('Debe iniciar sesión para realizar una compra.\n\nSerá redirigido a la página de login.');
					window.location.href = 'login.html';
				} else if (result.success) {
					alert(`¡Compra realizada! Total: $${totalPrice.toFixed(2)}\n\nGracias por tu compra.`);
					cart = [];
					updateCartInLocalStorage();
					updateCartCounter();
					updateCartMenu();
					containerListCart.classList.remove('show');
				} else {
					alert('Error al procesar la compra: ' + (result.message || 'Intenta nuevamente.'));
				}
			} catch (error) {
				console.error('Error:', error);
				alert('Error de conexión. Intenta nuevamente.');
			}
		}
	});
	listCart.appendChild(purchaseBtn);
};

const showHTML = () => {
	products.forEach(product => {
		const contentProduct = product.querySelector(
			'.content-card-product'
		);
		const productId = contentProduct.dataset.productId;
		const isFavorite = favorites.some(
			favorite => favorite.id === productId
		);

		const favoriteButton = product.querySelector('.favorite');
		const favoriteActiveButton =
			product.querySelector('#added-favorite');
		const favoriteRegularIcon = product.querySelector(
			'#favorite-regular'
		);
		favoriteButton.classList.toggle('favorite-active', isFavorite);
		favoriteRegularIcon.classList.toggle('active', isFavorite);
		favoriteActiveButton.classList.toggle('active', isFavorite);
	});


	counterFavorites.textContent = favorites.length;
	updateFavoriteMenu();
};

btnsFavorite.forEach(button => {
	button.addEventListener('click', e => {
		const card = e.target.closest('.content-card-product');

		const product = {
			id: card.dataset.productId,
			title: card.querySelector('h3').textContent,
			price: card.querySelector('.price').textContent,
			type: card.dataset.productType || detectProductType(),
		};

		toggleFavorite(product);

		showHTML();
	});
});

btnCart.forEach(button => {
	button.addEventListener('click', e => {
		const card = e.target.closest('.content-card-product');
		const typeFromAttr = card.dataset.productType;
		const typeDetected = detectProductType();

		const product = {
			id: card.dataset.productId,
			title: card.querySelector('h3').textContent,
			price: card.querySelector('.price').textContent,
			type: typeFromAttr || typeDetected,
		};

		console.log('Product Type from attribute:', typeFromAttr);
		console.log('Product Type detected:', typeDetected);
		console.log('Final product type:', product.type);

		addToCart(product);
	});
});

const btnClose = document.querySelector('#btn-close');
const buttonHeaderFavorite = document.querySelector(
	'#button-header-favorite'
);

const buttonHeaderCart = document.querySelector(
	'#button-header-cart'
);
const btnCloseCart = document.querySelector('#btn-close-cart');

if (buttonHeaderFavorite) {
	buttonHeaderFavorite.addEventListener('click', () => {
		containerListFavorites.classList.toggle('show');
	});
}

if (btnClose) {
	btnClose.addEventListener('click', () => {
		containerListFavorites.classList.remove('show');
	});
}

if (buttonHeaderCart) {
	buttonHeaderCart.addEventListener('click', () => {
		if (containerListCart) {
			containerListCart.classList.toggle('show');
		}
	});
}

if (btnCloseCart) {
	btnCloseCart.addEventListener('click', () => {
		if (containerListCart) {
			containerListCart.classList.remove('show');
		}
	});
}

loadFavoritesFromLocalStorage();
loadCartFromLocalStorage();
updateFavoriteMenu();
updateCartMenu();
updateCartCounter();
