import 'package:flutter/material.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/decorations/box_decorations.dart';
import '/core/helpers/push_go_router_helper.dart';

class FilterCardComponent extends StatelessWidget {
  const FilterCardComponent({super.key, required this.imageProvider});
  final ImageProvider imageProvider;
  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return GestureDetector(
      onTap: () =>
          pushGoRouterHelper(context: context, view: kFilterExamsView2Router),
      child: Container(
        height: size.height * (isRotait ? 0.05 : 0.1),
        width: size.width * 0.12,
        decoration: BoxDecorations.boxDecorationToFilterCardComponent(
          context: context,
          isRotait: isRotait,
          imageProvider: imageProvider,
        ),
      ),
    );
  }
}
