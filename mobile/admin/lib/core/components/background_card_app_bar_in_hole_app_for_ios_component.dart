import 'package:flutter/cupertino.dart';
import '/core/decorations/box_decorations.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';

class BackgroundCardAppBarInHoleAppForIosComponent extends StatelessWidget
    implements ObstructingPreferredSizeWidget {
  const BackgroundCardAppBarInHoleAppForIosComponent({
    super.key,
    required this.navigationbar,
    required this.height,
  });
  final Widget navigationbar;
  final double height;
  @override
  Widget build(BuildContext context) {
    return Stack(
      children: [
        Container(
          height: height,
          padding: OnlyPaddingWithoutChild.top15(context: context),
          decoration: BoxDecorations.boxDecorationToAppBarCard(),
        ),
        navigationbar,
      ],
    );
  }

  @override
  Size get preferredSize => Size.fromHeight(height);

  @override
  bool shouldFullyObstruct(BuildContext context) => false;
}
