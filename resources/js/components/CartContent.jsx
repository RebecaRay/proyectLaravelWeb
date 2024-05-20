import React, { useState, useEffect, useContext } from "react";
import { UserData } from './MainInicial';
import axios from "axios";


function CartContent({ cartItems, updateCartItems, removeProductFromCart, updateCartItemQuantity, }) {
  const userData = useContext(UserData);
  

  const [compraExitosa, setCompraExitosa] = useState(false);

  const [cartQuantities, setCartQuantities] = useState(
    cartItems.reduce((quantities, item) => {
      quantities[item.id] = item.quantity || 1;
      return quantities;
    }, {})
  ); 

  const updateQuantity = (productId, newQuantity) => {
    const updatedQuantity = Math.max(newQuantity, 1);
    const stock = cartItems.find(item => item.id === productId).stock;
    const finalQuantity = Math.min(updatedQuantity, stock);

    updateCartItems(productId, finalQuantity, () => {
      setCartQuantities(prevQuantities => ({
        ...prevQuantities,
        [productId]: finalQuantity,
      }));
    });
  };

  const totalAmount = cartItems.reduce(
    (total, item) => total + (item.price * (cartQuantities[item.id] || 0)),
    0
  );`
  `

  const handleCompraClick = async () => {
    console.log(userData.accessToken);
    console.log(userData);
    
    try {
      const authToken = userData.accessToken;

      if (!authToken) {
        console.error('Token de autorización no encontrado');
        return;
      }

      const user_id = userData && userData.user ? userData.user.id : null;

      if (!user_id) {
        console.error('ID de usuario no encontrado');
        return;
      }

      const total = cartItems.reduce((total, item) => {
        return total + (item.price * (cartQuantities[item.id] || 0));
      }, 0);


      console.log(user_id);

      const compraData = {
        
        productos: cartItems.map(item => ({
          user_id: user_id,
          product_id: item.id,
          name: item.name,
          price: item.price,
          total: total,
          quantity: cartQuantities[item.id] || 1,
        })),
      };

      console.log(compraData);

      const response = await axios.post('http://localhost/a4/a4/public/api/cart', compraData, {
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ' + authToken,
          'Accept': 'application/json',
          'X-CSRF-TOKEN': token,
        },
      });

      if (response.status === 200) {
        setCompraExitosa(true);
        updateCartItems([], true);
      } else {
        console.error('Error al realizar la compra:', response.data);
        setCompraExitosa(false);
      }
    } catch (error) {
      console.error('Error en handleCompraClick:', error);
      setCompraExitosa(false);
    }
  };

  const mostrarBotonComprar = cartItems.length > 0 && !compraExitosa;

  useEffect(() => {
    if (compraExitosa) {

      updateCartItems([], true);

      // Eliminar todos los productos del carrito (opcional)
    cartItems.forEach((item) => {
      removeProductFromCart(item.id);
    });
    
      alert('¡Gracias por tu compra!');
    }
  }, [compraExitosa]);

  return (
    <div style={{ position: "relative", maxWidth: "800px", margin: "auto", padding: "20px" }}>
        <h2 style={{ textAlign: "center" }}>Carrito de Compras</h2>

        {cartItems.length === 0 ? (
            <p style={{ textAlign: "center" }}>El carrito está vacío.</p>
        ) : (
            <table style={{ width: "100%" }}>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    {cartItems.map((item, index) => (
                        <tr key={index} style={{ marginBottom: index === cartItems.length - 1 ? "20px" : 0 }}>
                            <td>{item.name}</td>
                            <td>{item.price * cartQuantities[item.id]}$</td>
                            <td>
                                <div style={{ display: "flex", alignItems: "center" }}>
                                    <button onClick={() => updateQuantity(item.id, cartQuantities[item.id] - 1)}>-</button>
                                    <span style={{ margin: "0 8px" }}>{cartQuantities[item.id] || 1}</span>
                                    <button onClick={() => updateQuantity(item.id, cartQuantities[item.id] + 1)}>+</button>
                                </div>
                            </td>
                            <td>
                                <button onClick={() => removeProductFromCart(item.id)}>
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        )}

        <div style={{
            position: "fixed", bottom: 20, left: 0, width: "100%", background: "#fff", padding: "10px", borderTop: "1px solid #ccc", boxShadow: "0px -5px 5px -5px #333", zIndex: 2, display: "flex", justifyContent: "space-between",
        }}>
            <div style={{ textAlign: "right" }}>
                {cartItems.length > 0 && <strong>Total a pagar:</strong>} {totalAmount}$
            </div>
            {mostrarBotonComprar && (
                <button onClick={handleCompraClick} style={{ marginTop: "10px" }}>
                    Comprar
                </button>
            )}
        </div>
    </div>
);

}

export default CartContent;
