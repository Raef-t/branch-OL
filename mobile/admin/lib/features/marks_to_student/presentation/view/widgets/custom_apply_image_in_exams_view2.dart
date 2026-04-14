import 'package:flutter/material.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/helpers/push_go_router_helper.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/gen/assets.gen.dart';

class CustomApplyImageInExamsView2 extends StatelessWidget {
  const CustomApplyImageInExamsView2({super.key});

  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    return OnlyPaddingWithChild.left30(
      context: context,
      child: GestureDetector(
        onTap: () => pushGoRouterHelper(
          context: context,
          view: kExamsToStudentViewRouter,
        ),
        child: SizedBox(
          height: size.height * 0.035,
          width: size.width * 0.06,
          child: Assets.images.applyImage.image(),
        ),
      ),
    );
  }
}
