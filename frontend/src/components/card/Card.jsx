import { Component } from "react";
import { Link } from "react-router-dom";
import { CardStyel } from "./styles/card.styled";
import ProCartSvg from "../../../public/Empty Cart.svg";
import { connect } from "react-redux";
import { addToCart, updateCart } from "../../store/cartSlice";

class Card extends Component {
  getRndInteger(min, max) {
    return Math.floor(Math.random() * (max - min)) + min;
  }

  isSameAttribute(oldProduct, newProduct) {
    const oldProductAttrs = Object.values(oldProduct.selectedAttributes);
    const newProdutAttrs = Object.values(newProduct.selectedAttributes);
    return JSON.stringify(oldProductAttrs) === JSON.stringify(newProdutAttrs);
  }

  addProToCart() {
    const arr = [...this.props.product.attributes];
    const updatedProduct = {
      ...this.props.product,
      selectedAttributes: {
        ...this.props.product.selectedAttributes,
        selectedColor: 0,
      },
    };

    for (let index in arr) {
      updatedProduct.selectedAttributes[arr[index].id] = 0;
    }

    const addedProducts = this.props.cartItems.filter(
      (item) => item.id === updatedProduct.id
    );

    if (addedProducts.length > 0) {
      let sameProduct;

      for (let item of addedProducts) {
        if (this.isSameAttribute(item, updatedProduct)) {
          sameProduct = item.key;
        }
      }

      if (sameProduct) {
        this.props.updateCart(sameProduct);
      } else {
        this.props.addToCart({
          ...updatedProduct,
          key: this.getRndInteger(0, 100000),
        });
      }
    } else {
      this.props.addToCart({
        ...updatedProduct,
        key: this.getRndInteger(0, 100000),
      });
    }
  }

  componentDidUpdate() {
    localStorage.setItem("cartItems", JSON.stringify(this.props.cartItems));
  }

  render() {
    return (
      <CardStyel>
        <div
          className="pro-card"
          data-testid="product-${product name in kebab case}"
        >
          {this.props.product.inStock || (
            <Link to={`/description/${this.props.product.id}`}>
              <div className="outStock">
                <p> out of stock</p>
              </div>
            </Link>
          )}
          <Link to={`/description/${this.props.product.id}`}>
            <img
              src={this.props.product.gallery[0].image_url}
              alt={this.props.product.id}
              className="product-img"
            />
          </Link>
          {this.props.product.inStock && (
            <div className="cart-badge" onClick={() => this.addProToCart()}>
              <div className="c-img">
                <img src={ProCartSvg} alt="cart-logo" />
              </div>
            </div>
          )}

          <p className="product-name">
            {this.props.product.brand} {this.props.product.name}
          </p>
          <h3 className="price">
            {this.props.product.price[0].amount.toFixed(2)}
            {this.props.product.price[0].currency_symbol}
          </h3>
        </div>
      </CardStyel>
    );
  }
}

const mapStateToProps = (state) => ({
  cartItems: state.cart.cartItems,
});

const mapDispatchToProps = { addToCart, updateCart };

export default connect(mapStateToProps, mapDispatchToProps)(Card);
