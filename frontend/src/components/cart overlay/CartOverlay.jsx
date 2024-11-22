import { Component } from "react";
import CartItem from "./CartItem";
import { CartOverlayStyle } from "./styles/cartOverlay.styled";
import { Link } from "react-router-dom";
import { connect } from "react-redux";
import { setShowCart, setShowModal } from "../../store/cartSlice";

class CartOverlay extends Component {
  render() {
    return (
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
                {this.props.cartItems.reduce((acc, item) => acc + item.qty, 0)}
              </span>
              &nbsp; items
            </span>
          </h3>
          <div className="cart-overlay">
            {this.props.cartItems.length > 0 ? (
              this.props.cartItems.map((item, i) => (
                <CartItem
                  key={i}
                  item={item}
                  id={i}
                />
              ))
            ) : (
              <p>cart is empty</p>
            )}
          </div>
          {this.props.cartItems.length > 0 && (
            <>
              <p>
                Total &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <span>
                  {this.props.cartItems.length > 0 &&
                    this.props.cartItems
                      .reduce(
                        (acc, item) =>
                          acc +
                          item.price[0]?.amount *
                            item.qty,
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
                  }}
                >
                  PLACE ORDER
                </button>
              </div>
            </>
          )}
        </CartOverlayStyle>
      </>
    );
  }
}
const mapStateToProps = (state) => ({
  showCart: state.cart.showCart,
  cartItems: state.cart.cartItems,
});
const mapDispatchToProps = { setShowCart, setShowModal };

export default connect(mapStateToProps, mapDispatchToProps)(CartOverlay);
