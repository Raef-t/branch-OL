import 'package:flutter/material.dart';
import '/core/components/text_bold20_component.dart';
import '/core/styles/colors_style.dart';
import '/features/auth/presentation/view/widgets/custom_logo_image_in_auth_view.dart';
import '/gen/fonts.gen.dart';

class CustomHeaderSectionInAuthView extends StatelessWidget {
  const CustomHeaderSectionInAuthView({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Padding(
          padding: EdgeInsets.only(
            left: MediaQuery.sizeOf(context).width * 0.30,
          ),
          child: const Align(
            alignment: Alignment.centerLeft,
            child: CustomLogoImageInAuthView(),
          ),
        ),
        SizedBox(height: MediaQuery.sizeOf(context).height * 0.04),
        Padding(
          padding: EdgeInsets.only(
            left: MediaQuery.sizeOf(context).width * 0.27,
          ),
          child: const Align(
            alignment: Alignment.centerLeft,
            child: TextBold20Component(
              text: 'معهد العلماء للتعليم',
              color: ColorsStyle.littleVinicColor,
              fontFamily: FontFamily.tajawal,
            ),
          ),
        ),
      ],
    );
  }
}
