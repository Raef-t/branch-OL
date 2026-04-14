import 'package:flutter/material.dart';
import '/core/components/svg_image_component.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';
import '/features/home/presentation/view/widgets/custom_popup_menu_on_sliver_app_bar_in_home_view.dart';
import '/gen/assets.gen.dart';

class CustomLeadingAppBarInHomeView extends StatelessWidget {
  const CustomLeadingAppBarInHomeView({super.key});

  @override
  Widget build(BuildContext context) {
    double height = MediaQuery.sizeOf(context).height;
    return OnlyPaddingWithChild.left19(
      context: context,
      child: CustomPopupMenuOnSliverAppBarInHomeView(
        child: Row(
          children: [
            Assets.images.logoOlamaaImage.image(height: height * 0.049),
            Widths.width7(context: context),
            SvgImageComponent(
              pathImage: Assets.images.bottomArrowImage,
              color: ColorsStyle.littleVinicColor,
            ),
          ],
        ),
      ),
    );
  }
}
