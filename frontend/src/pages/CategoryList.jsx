import { Component } from "react";
import CardContainer from "../components/card/CardContainer";
import { GET_CategoryByName } from "../queries";
import { connect } from "react-redux";
import { getCategory } from "../store/categoriesSlice";

class Category extends Component {
  componentDidMount() {
    this.fetchCategory();
  }

  componentDidUpdate(prevProps) {
    if (prevProps.match.params.category !== this.props.match.params.category) {
      this.fetchCategory();
    }
  }

  fetchCategory = () => {
    const categoryName = this.props.match.params.category || "all";
    this.props.client
      .query({
        query: GET_CategoryByName,
        variables: {
          name: categoryName,
        },
      })
      .then((result) => {
        this.props.getCategory(result.data.category);
      })
      .catch((error) => {
        console.error("Error fetching category:", error);
      });
  };

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
