import { Component } from "react";
import { CartItemStyle } from "./styles/cartOverlay.styled";
import MinusSvg from "../../../public/minus-squ.svg";
import AddSvg from "../../../public/plus-squ.svg";
import { removeFromCart, updateCart, deletItem } from "../../store/cartSlice";
import { connect } from "react-redux";
import PrevArrowSvg from "../../../public/Group 1417.svg";
import AfterArrowSvg from "../../../public/Group 1418.svg";

class CartItem extends Component {
  constructor(props) {
    super(props);
    this.state = {
      index: 0,
    };
  }

  componentDidUpdate() {
    localStorage.setItem("cartItems", JSON.stringify(this.props.cartItems));
  }

  removeItem() {
    this.props.item.qty === 1
      ? this.props.deletItem(this.props.id)
      : this.props.removeFromCart(this.props.id);
  }

  after() {
    this.setState((prev) => ({
      ...prev,
      index:
        prev.index === this.props.item.gallery.length - 1
          ? prev.index
          : prev.index + 1,
    }));
  }

  previous() {
    this.setState((prev) => ({
      ...prev,
      index: prev.index === 0 ? prev.index : prev.index - 1,
    }));
  }

  render() {
    const price = this.props.item.price[0]; // Access the first price object

    return (
      <CartItemStyle>
        <div className="cart-item">
          <div className="product-info">
            <h2>{this.props.item.brand}</h2>
            <h2>{this.props.item.name}</h2>
            <div className="price">
              <span>
                {price.currency_symbol}
                {price.amount}
              </span>
            </div>
            <div
              className="attributes"
              data-testid="cart-item-attribute-${attribute name in kebab case}"
            >
              {this.props.item.attributes
                .filter((atr) => atr.id !== "Color")
                .map((d, i) => (
                  <div className="size" key={i}>
                    <h4 data-testid="cart-item-attribute-${attribute name in kebab case}-${attribute name in kebab case}">
                      {d.id}:
                    </h4>
                    <div className="size-box">
                      {d.items.map((size, i) => (
                        <div
                          key={i}
                          className={`size-x ${
                            this.props.item.selectedAttributes[d.id] === i
                              ? "selected"
                              : ""
                          } ${
                            this.props.item.selectedAttributes[d.id] ===
                              undefined && i === 0
                              ? "selected"
                              : ""
                          }`}
                          data-testid="cart-item-attribute-${attribute name in kebab case}-${attribute name in kebab case}-selected"
                        >
                          {size.value}
                        </div>
                      ))}
                    </div>
                  </div>
                ))}
              {this.props.item.attributes
                .filter((a) => a.id === "Color")
                .map((d, i) => (
                  <div className="color" key={i}>
                    <h4 data-testid="cart-item-attribute-${attribute name in kebab case}-${attribute name in kebab case}">
                      {d.id}:
                    </h4>
                    <div className="color-box">
                      {d.items.map((color, i) => (
                        <div
                          key={i}
                          className={`color-x ${
                            this.props.item.selectedAttributes.selectedColor ===
                            i
                              ? "selected"
                              : ""
                          }`}
                          style={{ backgroundColor: `${color.value}` }}
                          data-testid="cart-item-attribute-${attribute name in kebab case}-${attribute name in kebab case}-selected"
                        ></div>
                      ))}
                    </div>
                  </div>
                ))}
            </div>
          </div>
          <div className="product-count">
            <div className="inc">
              <img
                src={AddSvg}
                alt="plus"
                onClick={() => {
                  this.props.updateCart(this.props.item.key);
                }}
                data-testid="cart-item-amount-increase"
              />
            </div>
            <span>{this.props.item.qty}</span>
            <div className="dec">
              <img
                src={MinusSvg}
                alt="minus"
                onClick={() => this.removeItem()}
                data-testid="cart-item-amount-decrease"
              />
            </div>
          </div>
          <div className="pro-img">
            <img
              src={this.props.item.gallery[this.state.index].image_url}
              alt={this.props.item.id}
              className="product-img"
            />
            <div className="changing-box">
              <img
                src={PrevArrowSvg}
                alt="prev"
                className="prev"
                onClick={() => this.previous()}
              />
              <img
                src={AfterArrowSvg}
                alt="after"
                className="after"
                onClick={() => this.after()}
              />
            </div>
          </div>
        </div>
      </CartItemStyle>
    );
  }
}

const mapStateToProps = (state) => ({
  showCart: state.cart.showCart,
  cartItems: state.cart.cartItems,
});

const mapDispatchToProps = {
  removeFromCart,
  updateCart,
  deletItem,
};

export default connect(mapStateToProps, mapDispatchToProps)(CartItem);
