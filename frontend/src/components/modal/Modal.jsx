import { Component } from "react";
import { ModalStyle } from "./styles/Modal.styled";
import { Link } from "react-router-dom";
import { connect } from "react-redux";
import {
  setShowModal,
  resetCart,
  setActiveCategory,
} from "../../store/cartSlice";

class Modal extends Component {
  render() {
    return (
      <ModalStyle>
        <div id="myModal" className="modal">
          <div className="modal-content">
            <div className="modal-body">
              <p>Thank you for your visit</p>
              <p>Waiting for you again soon</p>
            </div>
            <div className="modal-footer">
              <Link
                to="/"
                onClick={() => {
                  this.props.setShowModal(false);
                  this.props.resetCart();
                  localStorage.setItem("cartItems", JSON.stringify([]));
                  this.props.setActiveCategory(0);
                }}
                className="btn"
              >
                GO HOME
              </Link>
            </div>
          </div>
        </div>
      </ModalStyle>
    );
  }
}

const mapStateToProps = (state) => ({
  showModal: state.cart.showModal,
});
const mapDispatchToProps = { setShowModal, resetCart, setActiveCategory };

export default connect(mapStateToProps, mapDispatchToProps)(Modal);
