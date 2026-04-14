import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';

class SliverAppBarToHoleAppComponent extends StatelessWidget {
  const SliverAppBarToHoleAppComponent({super.key, required this.appBarWidget});
  final Widget appBarWidget;
  @override
  Widget build(BuildContext context) {
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return SliverAppBar(
      automaticallyImplyLeading: false,
      // backgroundColor: ColorsStyle.transparentColor,
      expandedHeight:
          MediaQuery.sizeOf(context).height * (isRotait ? 0.155 : 0.22),
      collapsedHeight:
          MediaQuery.sizeOf(context).height * (isRotait ? 0.155 : 0.22),
      flexibleSpace: Container(
        decoration: BoxDecorations.boxDecorationToAppBarCard(),
        child: appBarWidget,
      ),
    );
  }
}
