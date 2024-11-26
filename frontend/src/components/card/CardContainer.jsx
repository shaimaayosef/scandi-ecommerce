import { Component } from "react";
import Card from "./Card";
import { CardStyel } from "./styles/card.styled";

export default class CardContainer extends Component {
  render() {
    return (
      <CardStyel>
        <div className="CardContainer">
          <div className="title">
            <h2>{this.props.category.name}</h2>
          </div>
          <div className="cardsDiv">
            {this.props.category.products.length > 0 &&
              this.props.category.products.map((product, index) => (
                <Card product={product} key={index} />
              ))}
          </div>
        </div>
      </CardStyel>
    );
  }
}
