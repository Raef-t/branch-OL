import 'package:flutter/material.dart';
import 'package:second_page_app/core/styles/colors_style.dart';
import '/core/components/scan_or_display_q_r_message_component.dart';
import '/core/components/svg_image_component.dart';
import '/core/helpers/show_little_black_view_with_widget_helper.dart';
import '/gen/assets.gen.dart';

class CircleQRInBottomNavigationBarComponent extends StatelessWidget {
  const CircleQRInBottomNavigationBarComponent({super.key});

  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.landscape;
    return GestureDetector(
      onTap: () => showLittleBlackViewWithWidgetHelper(
        context: context,
        widget: const ScanOrDisplayQRMessageComponent(),
      ),
      child: Container(
        width: 52,
        height: 52,
        decoration: const BoxDecoration(
          shape: BoxShape.circle,
          gradient: LinearGradient(
            begin: Alignment.centerLeft,
            end: Alignment.centerRight,
            colors: [
              ColorsStyle.colorQRButtonRight,
              ColorsStyle.colorQRButtonLeft,
            ],
          ),
          boxShadow: [
            BoxShadow(
              blurRadius: 16,
              offset: Offset(0, 6),
              color: Color(0x26000000),
            ),
          ],
        ),
        child: Center(
          child: SvgImageComponent(
            pathImage: Assets.images.qrImage,
            width: 24,
            height: 24,
            color: Colors.white,
          ),
        ),
      ),
    );
  }
}
