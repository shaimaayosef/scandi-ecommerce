import { Component } from "react";
import ProductView from "../components/product view/ProductView";
import { connect } from "react-redux";
import { GET_ProductById } from "../queries";

class ProductDescriptionPage extends Component {
  constructor(props) {
    super(props);
    this.state = {
      product: null,
    };
  }

  componentDidMount() {
    this.props.client
      .query({
        query: GET_ProductById,
        variables: { id: this.props.match.params.id },
      })
      .then((result) => this.setState({ product: result.data.product }));
  }

  render() {
    return (
      <div>
        {this.state.product ? (
          <ProductView product={this.state.product} />
        ) : (
          <p>loading...</p>
        )}
      </div>
    );
  }
}

const mapStateToProps = (state) => ({
  categories: state.categories.categories,
});

export default connect(mapStateToProps)(ProductDescriptionPage);
