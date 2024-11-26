import { Component } from "react";
import { Routes, Route, useParams } from "react-router-dom";
import Navbar from "./components/navbar/Navbar.jsx";
import CartOverlay from "./components/cart overlay/CartOverlay.jsx";
import ProductDescriptionPage from "./pages/ProductDescriptionPage.jsx";
import { GET_CATEGORIES } from "./queries.jsx";
import { connect } from "react-redux";
import { getCategories } from "./store/categoriesSlice";
import Modal from "./components/modal/Modal.jsx";
import CategoryList from "./pages/CategoryList.jsx";


class App extends Component {
  componentDidMount() {
    this.props.client
      .query({
        query: GET_CATEGORIES,
      })
      .then((result) => {
        this.props.getCategories(result.data.categories);
      })
      .catch((error) => {
        console.error("Error fetching categories:", error);
      });
  }
  render() {
    const Wrapper = (props) => {
      const params = useParams();
      return (
        <ProductDescriptionPage
          {...{ ...props, match: { params }, client: this.props.client }}
        />
      );
    };
    const CategoryWrapper = (props) => {
      const params = useParams();
      return (
        <CategoryList
          {...{ ...props, match: { params }, client: this.props.client }}
        />
      );
    };
    return (
      <div>
        <Navbar categories={this.props.categories} />
        {this.props.showCart && <CartOverlay />}
        {this.props.showModal && <Modal />}
        <div>
          {this.props.categories.length > 0 ? (
            <Routes>
              <Route path="/" element={<CategoryWrapper />} />
              <Route path="/:category" element={<CategoryWrapper />} />
              <Route path="/description/:id" element={<Wrapper />} />
            </Routes>
          ) : (
            <p>loading...</p>
          )}
        </div>
      </div>
    );
  }
}

const mapStateToProps = (state) => {
  return {
    categories: state.categories.categories,
    showCart: state.cart.showCart,
    showModal: state.cart.showModal,
  };
};

const mapDispatchToProps = { getCategories };

export default connect(mapStateToProps, mapDispatchToProps)(App);
