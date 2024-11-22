import { Component } from "react";
import CardContainer from "../components/card/CardContainer";
import { GET_CategoryByName } from "../queries";
import { connect } from "react-redux";
import { getCategory } from "../store/categoriesSlice";

class Category extends Component {
  componentDidMount() {
    this.props.client
      .query({
        query: GET_CategoryByName,
        variables: {
          name: this.props.match.params.category || "all" ,
        },
      })
      .then((result) => this.props.getCategory(result.data));
  }

  componentDidUpdate(prevProps) {
    if (prevProps.match.params.category !== this.props.match.params.category) {
      this.props.client
        .query({
          query: GET_CategoryByName,
          variables: {
            name:  this.props.match.params.category || "all" ,
          },
        })
        .then((result) => this.props.getCategory(result.data.category));
    }
  }

  render() {
    return (
      <>
        {this.props.category?.name ? (
          <CardContainer category={this.props.category} />
        ) : (
          <div>wait....</div>
        )}
      </>
    );
  }
}


const mapStateToProps = (state) => ({
  category: state.categories.category,
});

const mapDispatchToProps = { getCategory };

export default connect(mapStateToProps, mapDispatchToProps)(Category);