import 'package:flutter/material.dart';
import '/core/helpers/pop_go_router_helper.dart';

class RightArrowImageInSliverAppBarWithClickComponent extends StatelessWidget {
  const RightArrowImageInSliverAppBarWithClickComponent({
    super.key,
    required this.image,
  });
  final Image image;
  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    return GestureDetector(
      onTap: () => popGoRouterHelper(context: context),
      child: SizedBox(
        height: size.height * 0.035,
        width: size.width * 0.06,
        child: image,
      ),
    );
  }
}
