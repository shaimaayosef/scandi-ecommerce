import { Component } from "react";
import { Link } from "react-router-dom";
import { connect } from "react-redux";
import { NavStyle } from "./styles/Nav.styled.jsx";
import logotSvg from "../../../public/a-logo.svg";
import cartSvg from "../../../public/EmptyCart.svg";
import { setShowCart, setActiveCategory } from "../../store/cartSlice.jsx";

class Navbar extends Component {
  handleItemClick(index) {
    this.props.setActiveCategory(index);
  }

  render() {
    return (
      <NavStyle>
        <nav className="navbar">
          <ul
            className="catogeries"
            onClick={() => this.props.setShowCart(false)}
          >
            {this.props.categories.length > 0 &&
              this.props.categories.map((item, index) => (
                <Link
                  key={index}
                  to={item.name === "all" ? "/" : "/" + item.name}
                  className={`nav-links ${
                    this.props.activeCategory === index ? "active" : ""
                  }`}
                  data-testid={
                    this.props.activeCategory === index
                      ? "active-category-link"
                      : "category-link"
                  }
                >
                  <li
                    className={
                      this.props.activeCategory === index ? "active" : ""
                    }
                    onClick={this.handleItemClick.bind(this, index)}
                  >
                    {item.name}
                  </li>
                </Link>
              ))}
          </ul>
          <Link to="/">
            <img
              src={logotSvg}
              alt="nav logo"
              className="nav-logo"
              onClick={() => {
                this.handleItemClick(0);
                this.props.setShowCart(false);
              }}
            />
          </Link>
          <div className="cart-logo">
            <div className="cart">
              <div className="badge">
                <span>
                  {this.props.cartItems.reduce(
                    (acc, item) => acc + item.qty,
                    0
                  )}
                </span>
              </div>
              <img
                src={cartSvg}
                alt="cart logo"
                className="cart-img"
                onClick={() => this.props.setShowCart(!this.props.showCart)}
                data-testid="cart-btn"
              />
            </div>
          </div>
        </nav>
      </NavStyle>
    );
  }
}

const mapStateToProps = (state) => ({
  showCart: state.cart.showCart,
  cartItems: state.cart.cartItems,
  activeCategory: state.cart.activeCategory,
});

const mapDispatchToProps = {
  setShowCart,
  setActiveCategory,
};

export default connect(mapStateToProps, mapDispatchToProps)(Navbar);
