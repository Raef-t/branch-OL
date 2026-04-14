import 'package:flutter/cupertino.dart';
import 'package:flutter_svg/svg.dart';

class SvgImageComponent extends StatelessWidget {
  const SvgImageComponent({
    super.key,
    required this.pathImage,
    required this.color,
    this.width,
    this.height,
  });
  final String pathImage;
  final Color color;
  final double? width;
  final double? height;
  @override
  Widget build(BuildContext context) {
    return SvgPicture.asset(
      width: width,
      height: height,
      pathImage,
      colorFilter: ColorFilter.mode(color, BlendMode.srcIn),
    );
  }
}
