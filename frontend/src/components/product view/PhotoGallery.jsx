import React from "react";
import PrevArrowSvg from "../../../public/Group 1417.svg";
import AfterArrowSvg from "../../../public/Group 1418.svg";
import { PhotoGalleryStyle } from "./styles/PhotoGallery.styled";

class PhotoGallery extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      currentIndex: 0,
      src: props.product.gallery[0].image_url,
    };
  }

  after = () => {
    if (this.props.product.gallery && this.props.product.gallery.length > 0) {
      const nextIndex =
        (this.state.currentIndex + 1) % this.props.product.gallery.length;
      this.setState({
        currentIndex: nextIndex,
        src: this.props.product.gallery[nextIndex].image_url,
      });
    }
  };

  previous = () => {
    if (this.props.product.gallery && this.props.product.gallery.length > 0) {
      const prevIndex =
        (this.state.currentIndex - 1 + this.props.product.gallery.length) %
        this.props.product.gallery.length;
      this.setState({
        currentIndex: prevIndex,
        src: this.props.product.gallery[prevIndex].image_url,
      });
    }
  };

  changeImg(src) {
    this.setState((prevState) => ({
      ...prevState,
      src: src,
    }));
  }

  render() {
    return (
      <PhotoGalleryStyle>
        <div className="product-images" data-testid="product-gallery">
          <div className="Photo-thumbinals">
            {this.props.product.gallery.map((image, index) => (
              <div key={index} className="image">
                <img
                  src={image.image_url}
                  alt={image.image_url}
                  onClick={() => this.changeImg(image.image_url)}
                />
              </div>
            ))}
          </div>

          <div className="Photo-Gallery">
            {this.props.product.inStock || (
              <div className="outStock">
                <p> out of stock</p>
              </div>
            )}
            <img src={this.state.src} alt={this.props.product.name} />
            <img
              src={PrevArrowSvg}
              alt="prev"
              className="prev"
              onClick={this.previous}
            />
            <img
              src={AfterArrowSvg}
              alt="after"
              className="after"
              onClick={this.after}
            />
          </div>
        </div>
      </PhotoGalleryStyle>
    );
  }
}

export default PhotoGallery;
