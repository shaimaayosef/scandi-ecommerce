import { Component } from "react";
import { ApolloConsumer } from "@apollo/client";
import CartItem from "./CartItem";
import { CartOverlayStyle } from "./styles/cartOverlay.styled";
import { PLACE_ORDER_MUTATION } from "../../queries";

import { connect } from "react-redux";
import { setShowCart, setShowModal } from "../../store/cartSlice";

class CartOverlay extends Component {
  gatherProductInfo = () => {
    const productInfo = this.props.cartItems.map((item) => ({
      id: item.id,
      name: item.name,
      price: item.price[0]?.amount,
      quantity: item.qty,
    }));
    return productInfo;
  };

  handlePlaceOrder = async (client) => {
    const total = parseFloat(
      this.props.cartItems
        .reduce((acc, item) => acc + item.price[0]?.amount * item.qty, 0)
        .toFixed(2)
    );
    const products = this.gatherProductInfo();
    try {
      // Execute the GraphQL mutation to place an order
      const result = await client.mutate({
        mutation: PLACE_ORDER_MUTATION,
        variables: {
          orderData: {
            total,
            products,
          },
        },
      });

      if (result.data.placeOrder) {
        alert("Order placed successfully!");
      } else {
        alert("Failed to place order.");
      }
    } catch (error) {
      console.error("Error placing order:", error);
    }
  };

  render() {
    return (
      <ApolloConsumer>
        {(client) => (
          <>
            <div
              className="overlay"
              onClick={() => this.props.setShowCart(false)}
            ></div>
            <CartOverlayStyle>
              <h3>
                My Bag, &nbsp;
                <span>
                  <span className="bold">
                    {this.props.cartItems.reduce(
                      (acc, item) => acc + item.qty,
                      0
                    )}
                  </span>
                  &nbsp; items
                </span>
              </h3>
              <div className="cart-overlay">
                {this.props.cartItems.length > 0 ? (
                  this.props.cartItems.map((item, i) => (
                    <CartItem key={i} item={item} id={i} />
                  ))
                ) : (
                  <p>cart is empty</p>
                )}
              </div>
              {this.props.cartItems.length > 0 && (
                <>
                  <p>
                    Total &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                    &nbsp; &nbsp; &nbsp;
                    <span data-testid="cart-total">
                      {this.props.cartItems.length > 0 &&
                        this.props.cartItems
                          .reduce(
                            (acc, item) =>
                              acc + item.price[0]?.amount * item.qty,
                            0
                          )
                          .toFixed(2)}
                    </span>
                  </p>

                  <div className="btn">
                    <button
                      className="check-btn"
                      onClick={() => {
                        this.props.setShowModal(true);
                        this.props.setShowCart(false);
                        this.handlePlaceOrder(client);
                      }}
                    >
                      PLACE ORDER
                    </button>
                  </div>
                </>
              )}
            </CartOverlayStyle>
          </>
        )}
      </ApolloConsumer>
    );
  }
}

const mapStateToProps = (state) => ({
  showCart: state.cart.showCart,
  cartItems: state.cart.cartItems,
});
const mapDispatchToProps = { setShowCart, setShowModal };

export default connect(mapStateToProps, mapDispatchToProps)(CartOverlay);
