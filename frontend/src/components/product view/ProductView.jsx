import { Component } from "react";
import { ProductViewStyle } from "./styles/ProductView.styled";
import { connect } from "react-redux";
import { addToCart, updateCart } from "../../store/cartSlice";
import { EditorState, Editor } from "draft-js";
import { convertFromHTML } from "draft-convert";
import PhotoGallery from "./PhotoGallery";

class ProductView extends Component {
  constructor(props) {
    super(props);
    this.state = {
      currentIndex: 0,
      src: props.product.gallery[0].image_url,
      selectedAttributes: {},
      selectedColor: 0,
      editorState: EditorState.createEmpty(),
    };
  }

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
        selectedColor: this.state.selectedColor,
      },
    };

    for (let index in arr) {
      updatedProduct.selectedAttributes[arr[index].id] =
        this.state.selectedAttributes[arr[index].id] ?? 0;
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

  onChange = (editorState) => {
    this.setState({
      editorState,
    });
  };

  componentDidMount() {
    const { editorState } = this.state;
    this.onChange(
      EditorState.push(
        editorState,
        convertFromHTML(this.props.product.description)
      )
    );
    localStorage.setItem("cartItems", JSON.stringify(this.props.cartItems));
  }

  render() {
    const price = this.props.product.price[0]; // Access the first price object

    return (
      <ProductViewStyle>
        <div className="ProductView">
          <PhotoGallery
            className="product-images"
            product={this.props.product}
            renderGallery={this.renderGallery}
          />
          <div className="product-info">
            <h2>{this.props.product.brand}</h2>
            <h3>{this.props.product.name}</h3>
            <div
              className="attributes-container"
              data-testid="product-attribute-${attribute in kebab case}"
            >
              {this.props.product.attributes
                .filter((atr) => atr.id !== "Color")
                .map((d, i) => (
                  <div className="size" key={i}>
                    <h4>{d.id}:</h4>
                    <div className="size-box">
                      {d.items.map((size, i) => (
                        <div
                          key={i}
                          className={`size-x ${
                            this.state.selectedAttributes[d.id] === i
                              ? "selected"
                              : ""
                          } ${
                            this.state.selectedAttributes[d.id] === undefined &&
                            i === 0
                              ? "selected"
                              : ""
                          }`}
                          onClick={() => {
                            this.setState((prevState) => ({
                              ...prevState,
                              selectedAttributes: {
                                ...prevState.selectedAttributes,
                                [d.id]: i,
                              },
                            }));
                          }}
                        >
                          {size.value}
                        </div>
                      ))}
                    </div>
                  </div>
                ))}
              {this.props.product.attributes
                .filter((a) => a.id === "Color")
                .map((d, i) => (
                  <div className="color" key={i}>
                    <h4>Color:</h4>
                    <div className="color-box">
                      {d.items.map((color, i) => (
                        <div
                          key={i}
                          className={`color-x ${
                            this.state.selectedColor === i ? "selected" : ""
                          }`}
                          style={{ backgroundColor: `${color.value}` }}
                          onClick={() => {
                            this.setState((prevState) => ({
                              ...prevState,
                              selectedColor: i,
                            }));
                          }}
                        ></div>
                      ))}
                    </div>
                  </div>
                ))}
            </div>
            <div className="price">
              <h4 className="price">price:</h4>
              <span>
                {price.currency_symbol}
                {price.amount}
              </span>
            </div>
            {this.props.product.inStock ? (
              <button
                className="add"
                onClick={() => this.addProToCart()}
                data-testid="add-to-cart"
              >
                add to card
              </button>
            ) : (
              <p className="out">out of stock</p>
            )}
            <div className="descreption" data-testid="product-description">
              <Editor editorState={this.state.editorState} />
            </div>
          </div>
        </div>
      </ProductViewStyle>
    );
  }
}

const mapStateToProps = (state) => ({
  cartItems: state.cart.cartItems,
});

const mapDispatchToProps = { addToCart, updateCart };

export default connect(mapStateToProps, mapDispatchToProps)(ProductView);
